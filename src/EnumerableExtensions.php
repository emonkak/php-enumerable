<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Exception\MoreThanOneElementException;
use Emonkak\Enumerable\Exception\NoSuchElementException;
use Emonkak\Enumerable\Internal\Converters;
use Emonkak\Enumerable\Internal\Errors;
use Emonkak\Enumerable\Internal\IdentityFunction;
use Emonkak\Enumerable\Iterator\BufferIterator;
use Emonkak\Enumerable\Iterator\CatchIterator;
use Emonkak\Enumerable\Iterator\ConcatIterator;
use Emonkak\Enumerable\Iterator\DefaultIfEmptyIterator;
use Emonkak\Enumerable\Iterator\DistinctIterator;
use Emonkak\Enumerable\Iterator\DistinctUntilChangedIterator;
use Emonkak\Enumerable\Iterator\DoIterator;
use Emonkak\Enumerable\Iterator\DoWhileIterator;
use Emonkak\Enumerable\Iterator\EmptyIterator;
use Emonkak\Enumerable\Iterator\ExceptIterator;
use Emonkak\Enumerable\Iterator\FinallyIterator;
use Emonkak\Enumerable\Iterator\GroupByIterator;
use Emonkak\Enumerable\Iterator\GroupJoinIterator;
use Emonkak\Enumerable\Iterator\IntersectIterator;
use Emonkak\Enumerable\Iterator\JoinIterator;
use Emonkak\Enumerable\Iterator\MemoizeIterator;
use Emonkak\Enumerable\Iterator\OnErrorResumeNextIterator;
use Emonkak\Enumerable\Iterator\OrderByIterator;
use Emonkak\Enumerable\Iterator\OuterJoinIterator;
use Emonkak\Enumerable\Iterator\RepeatIterator;
use Emonkak\Enumerable\Iterator\RetryIterator;
use Emonkak\Enumerable\Iterator\ReverseIterator;
use Emonkak\Enumerable\Iterator\ScanIterator;
use Emonkak\Enumerable\Iterator\SelectIterator;
use Emonkak\Enumerable\Iterator\SelectManyIterator;
use Emonkak\Enumerable\Iterator\SkipIterator;
use Emonkak\Enumerable\Iterator\SkipLastIterator;
use Emonkak\Enumerable\Iterator\SkipWhileIterator;
use Emonkak\Enumerable\Iterator\StartWithIterator;
use Emonkak\Enumerable\Iterator\TakeIterator;
use Emonkak\Enumerable\Iterator\TakeLastIterator;
use Emonkak\Enumerable\Iterator\TakeWhileIterator;
use Emonkak\Enumerable\Iterator\UnionIterator;
use Emonkak\Enumerable\Iterator\WhereIterator;
use Emonkak\Enumerable\Iterator\WhileIterator;
use Emonkak\Enumerable\Iterator\ZipIterator;

/**
 * @template TSource
 */
trait EnumerableExtensions
{
    /**
     * @template TResult
     * @param TResult $seed
     * @param callable(TResult,TSource):TResult $func
     * @return TResult
     */
    public function aggregate(mixed $seed, callable $func): mixed
    {
        $result = $seed;
        /** @var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            $result = $func($result, $element);
        }
        return $result;
    }

    /**
     * @param ?callable(TSource):bool $predicate
     */
    public function all(?callable $predicate = null): bool
    {
        /** @var callable(TSource):bool */
        $predicate = $predicate ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            if (!$predicate($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ?callable(TSource):bool $predicate
     */
    public function any(?callable $predicate = null): bool
    {
        /** @var callable(TSource):bool */
        $predicate = $predicate ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            if ($predicate($element)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ?callable(TSource):numeric $selector
     * @throws NoSuchElementException
     */
    public function average(?callable $selector = null): float|int
    {
        /** @var callable(TSource):numeric */
        $selector = $selector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        $sum = 0;
        $count = 0;
        foreach ($source as $element) {
            $sum += $selector($element);
            $count++;
        }
        if ($count === 0) {
            throw Errors::noElements();
        }
        return $sum / $count;
    }

    /**
     * @return EnumerableInterface<TSource[]>
     */
    public function buffer(int $count, ?int $skip = null): EnumerableInterface
    {
        if ($skip === null) {
            $skip = $count;
        }
        if ($count <= 0) {
            throw Errors::argumentOutOfRange('count');
        }
        if ($skip <= 0) {
            throw Errors::argumentOutOfRange('skip');
        }
        return new BufferIterator($this->getSource(), $count, $skip);
    }

    /**
     * @param callable(\Exception):iterable<TSource> $handler
     * @return EnumerableInterface<TSource>
     */
    public function catch(callable $handler): EnumerableInterface
    {
        return new CatchIterator($this->getSource(), $handler);
    }

    /**
     * @param iterable<TSource> $sources
     * @return EnumerableInterface<TSource>
     */
    public function concat(iterable ...$sources): EnumerableInterface
    {
        return new ConcatIterator(array_merge([$this->getSource()], $sources));
    }

    /**
     * @param ?callable(TSource):bool $predicate
     */
    public function count(?callable $predicate = null): int
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate !== null) {
            $count = 0;
            foreach ($source as $element) {
                if ($predicate($element)) {
                    $count++;
                }
            }
            return $count;
        } else {
            if (is_array($source) || $source instanceof \Countable) {
                return count($source);
            }
            return iterator_count($source);
        }
    }

    /**
     * @param TSource $defaultValue
     * @return EnumerableInterface<TSource>
     */
    public function defaultIfEmpty(mixed $defaultValue): EnumerableInterface
    {
        return new DefaultIfEmptyIterator($this->getSource(), $defaultValue);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return DistinctIterator<TSource,TKey>
     */
    public function distinct(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): DistinctIterator
    {
        /** @var callable(TSource):TKey */
        $keySelector = $keySelector ?? IdentityFunction::get();
        /** @var EqualityComparer<TKey> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new DistinctIterator($source, $keySelector, $comparer);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @return DistinctUntilChangedIterator<TSource,TKey>
     */
    public function distinctUntilChanged(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): DistinctUntilChangedIterator
    {
        /** @var callable(TSource):TKey */
        $keySelector = $keySelector ?? IdentityFunction::get();
        /** @var EqualityComparer<TKey> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new DistinctUntilChangedIterator($source, $keySelector, $comparer);
    }

    /**
     * @param callable(TSource):void $action
     * @return EnumerableInterface<TSource>
     */
    public function do(callable $action): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new DoIterator($source, $action);
    }

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<TSource>
     */
    public function doWhile(callable $condition): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new DoWhileIterator($source, $condition);
    }

    /**
     * @return TSource
     * @throws NoSuchElementException
     */
    public function elementAt(int $index): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if (is_array($source)) {
            if (isset($source[$index])) {
                $element = $source[$index];
                return $element;
            }
        } elseif ($source instanceof \SeekableIterator) {
            $source->seek($index);
            if ($source->valid()) {
                $element = $source->current();
                return $element;
            }
        } else {
            foreach ($source as $i => $element) {
                if ($index === $i) {
                    return $element;
                }
            }
        }
        throw Errors::noElements();
    }

    /**
     * @template TDefault
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function elementAtOrDefault(int $index, mixed $defaultValue = null): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if (is_array($source)) {
            if (isset($source[$index])) {
                $element = $source[$index];
                return $element;
            }
        } elseif ($source instanceof \SeekableIterator) {
            $source->seek($index);
            if ($source->valid()) {
                $element = $source->current();
                return $element;
            }
        } else {
            foreach ($source as $i => $element) {
                if ($index === $i) {
                    return $element;
                }
            }
        }
        return $defaultValue;
    }

    /**
     * @param iterable<TSource> $second
     * @param ?EqualityComparerInterface<TSource> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function except(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        /** @var EqualityComparer<TSource> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new ExceptIterator($source, $second, $comparer);
    }

    /**
     * @param callable():void $finallyAction
     * @return EnumerableInterface<TSource>
     */
    public function finally(callable $finallyAction): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new FinallyIterator($source, $finallyAction);
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return TSource
     * @throws NoSuchElementException
     */
    public function first(?callable $predicate = null): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate !== null) {
            foreach ($source as $element) {
                if ($predicate($element)) {
                    return $element;
                }
            }
        } else {
            foreach ($source as $element) {
                return $element;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @template TDefault
     * @param ?callable(TSource):bool $predicate
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function firstOrDefault(?callable $predicate = null, mixed $defaultValue = null): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate !== null) {
            foreach ($source as $element) {
                if ($predicate($element)) {
                    return $element;
                }
            }
        } else {
            foreach ($source as $element) {
                return $element;
            }
        }
        return $defaultValue;
    }

    /**
     * @param callable(TSource):void $action
     */
    public function forEach(callable $action): void
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            $action($element);
        }
    }

    /**
     * @template TKey
     * @template TElement
     * @template TResult
     * @param callable(TSource):TKey $keySelector
     * @param ?callable(TSource):TElement $elementSelector
     * @param ?callable(TKey,TElement[]):TResult $resultSelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TResult>
     */
    public function groupBy(callable $keySelector, ?callable $elementSelector = null, ?callable $resultSelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        /** @var callable(TSource):TElement */
        $elementSelector = $elementSelector ?? IdentityFunction::get();
        /** @var callable(TKey,TElement[]):TResult */
        $resultSelector = $resultSelector ??
            /**
             * @param TKey $key
             * @param TElement[] $values
             * @return array{0:TKey,1:TElement[]}
             */
            static function($key, array $values): array {
                return [$key, $values];
            };
        /** @var EqualityComparer<TKey> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new GroupByIterator($source, $keySelector, $elementSelector, $resultSelector, $comparer);
    }

    /**
     * @template TInner
     * @template TKey
     * @template TResult
     * @param iterable<TInner> $inner
     * @param callable(TSource):TKey $outerKeySelector
     * @param callable(TInner):TKey $innerKeySelector
     * @param callable(TSource,TInner[]):TResult $resultSelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TResult>
     */
    public function groupJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        /** @var EqualityComparer<TKey> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new GroupJoinIterator($source, $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function ignoreElements(): EnumerableInterface
    {
        return new EmptyIterator();
    }

    /**
     * @param iterable<TSource> $second
     * @param ?EqualityComparerInterface<TSource> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function intersect(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        /** @var EqualityComparer<TSource> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new IntersectIterator($source, $second, $comparer);
    }

    public function isEmpty(): bool
    {
        foreach ($this->getSource() as $_) {
            return false;
        }
        return true;
    }

    /**
     * @template TInner
     * @template TKey
     * @template TResult
     * @param iterable<TInner> $inner
     * @param callable(TSource):TKey $outerKeySelector
     * @param callable(TInner):TKey $innerKeySelector
     * @param callable(TSource,TInner):TResult $resultSelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TResult>
     */
    public function join(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        /** @var EqualityComparer<TKey> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new JoinIterator($source, $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return TSource
     * @throws NoSuchElementException
     */
    public function last(?callable $predicate = null): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate !== null) {
            $hasValue = false;
            $value = null;
            foreach ($source as $element) {
                if ($predicate($element)) {
                    $value = $element;
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                /** @var TSource $value */
                return $value;
            }
        } else {
            $hasValue = false;
            $value = null;
            foreach ($source as $element) {
                $hasValue = true;
                $value = $element;
            }
            if ($hasValue) {
                /** @var TSource $value */
                return $value;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @template TDefault
     * @param ?callable(TSource):bool $predicate
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function lastOrDefault(?callable $predicate = null, $defaultValue = null): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate !== null) {
            $hasValue = false;
            $value = null;
            foreach ($source as $element) {
                if ($predicate($element)) {
                    $value = $element;
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                return $value;
            }
        } else {
            $hasValue = false;
            $value = null;
            foreach ($source as $element) {
                $hasValue = true;
                $value = $element;
            }
            if ($hasValue) {
                return $value;
            }
        }
        return $defaultValue;
    }

    /**
     * @template TValue
     * @param ?callable(TSource):TValue $selector
     * @return ?TValue
     */
    public function max(?callable $selector = null): mixed
    {
        /** @var callable(TSource):TValue */
        $selector = $selector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        $max = null;
        foreach ($source as $element) {
            $value = $selector($element);
            if ($max === null || $max < $value) {
                $max = $value;
            }
        }
        return $max;
    }

    /**
     * @template TValue
     * @param callable(TSource):TValue $keySelector
     * @return EnumerableInterface<TSource>
     */
    public function maxBy(callable $keySelector): EnumerableInterface
    {
        /** @var \Iterator<TSource> */
        $iterator = $this->toIterator();
        $iterator->rewind();

        $result = [];

        if ($iterator->valid()) {
            $element = $iterator->current();
            $resultKey = $keySelector($element);
            $result[] = $element;
            $iterator->next();

            while ($iterator->valid()) {
                $element = $iterator->current();
                $key = $keySelector($element);
                if ($key == $resultKey) {
                    $result[] = $element;
                } elseif ($key > $resultKey) {
                    $resultKey = $key;
                    $result = [$element];
                }
                $iterator->next();
            }
        }

        return new Sequence($result);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function memoize(): EnumerableInterface
    {
        return new MemoizeIterator($this->toIterator());
    }

    /**
     * @template TValue
     * @param ?callable(TSource):TValue $selector
     * @return ?TValue
     */
    public function min(?callable $selector = null): mixed
    {
        /** @var callable(TSource):TValue */
        $selector = $selector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        $min = null;
        foreach ($source as $element) {
            $value = $selector($element);
            if ($min === null || $min > $value) {
                $min = $value;
            }
        }
        return $min;
    }

    /**
     * @template TValue
     * @param callable(TSource):TValue $keySelector
     * @return EnumerableInterface<TSource>
     */
    public function minBy(callable $keySelector): EnumerableInterface
    {
        /** @var \Iterator<TSource> */
        $iterator = $this->toIterator();
        $iterator->rewind();

        $result = [];

        if ($iterator->valid()) {
            $element = $iterator->current();
            $resultKey = $keySelector($element);
            $result[] = $element;
            $iterator->next();

            while ($iterator->valid()) {
                $element = $iterator->current();
                $key = $keySelector($element);
                if ($key == $resultKey) {
                    $result[] = $element;
                } elseif ($key < $resultKey) {
                    $resultKey = $key;
                    $result = [$element];
                }
                $iterator->next();
            }
        }

        return new Sequence($result);
    }

    /**
     * @param iterable<TSource> $sources
     * @return EnumerableInterface<TSource>
     */
    public function onErrorResumeNext(iterable ...$sources): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new OnErrorResumeNextIterator(array_merge([$source], $sources));
    }

    /**
     * @template TInner
     * @template TKey
     * @template TResult
     * @param iterable<TInner> $inner
     * @param callable(TSource):TKey $outerKeySelector
     * @param callable(TInner):TKey $innerKeySelector
     * @param callable(TSource,?TInner):TResult $resultSelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TResult>
     */
    public function outerJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        /** @var EqualityComparer<TKey> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new OuterJoinIterator($source, $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderBy(?callable $keySelector = null): OrderedEnumerableInterface
    {
        /** @var callable(TSource):TKey */
        $keySelector = $keySelector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new OrderByIterator($source, $keySelector, false);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderByDescending(?callable $keySelector = null): OrderedEnumerableInterface
    {
        /** @var callable(TSource):TKey */
        $keySelector = $keySelector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new OrderByIterator($source, $keySelector, true);
    }

    /**
     * @param ?int $count
     * @return EnumerableInterface<TSource>
     */
    public function repeat(?int $count = null): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new RepeatIterator($source, $count);
    }

    /**
     * @param ?int $retryCount
     * @return EnumerableInterface<TSource>
     */
    public function retry(?int $retryCount = null): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new RetryIterator($source, $retryCount);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function reverse(): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new ReverseIterator($source);
    }

    /**
     * @template TAccumulate
     * @param TAccumulate $seed
     * @param callable(TAccumulate,TSource):TAccumulate $func
     * @return EnumerableInterface<TAccumulate[]>
     */
    public function scan(mixed $seed, callable $func): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new ScanIterator($source, $seed, $func);
    }

    /**
     * @template TResult
     * @param callable(TSource,array-key):TResult $selector
     * @return EnumerableInterface<TResult>
     */
    public function select(callable $selector): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new SelectIterator($source, $selector);
    }

    /**
     * @template TResult
     * @param callable(TSource,array-key):iterable<TResult> $collectionSelector
     * @return EnumerableInterface<TResult>
     */
    public function selectMany(callable $collectionSelector): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new SelectManyIterator($source, $collectionSelector);
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return TSource
     * @throws NoSuchElementException
     * @throws MoreThanOneElementException
     */
    public function single(?callable $predicate = null): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate !== null) {
            $value = null;
            $hasValue = false;

            foreach ($source as $element) {
                if ($predicate($element)) {
                    if ($hasValue) {
                        throw Errors::moreThanOneMatch();
                    }
                    $value = $element;
                    $hasValue = true;
                }
            }

            if ($hasValue) {
                /** @var TSource $value */
                return $value;
            }
        } else {
            if (is_array($source)) {
                switch (count($source)) {
                    case 0:
                        throw Errors::noElements();
                    case 1:
                        return reset($source);
                    default:
                        throw Errors::moreThanOneMatch();
                }
            } else {
                $value = null;
                $hasValue = false;

                foreach ($source as $element) {
                    if ($hasValue) {
                        throw Errors::moreThanOneMatch();
                    }
                    $value = $element;
                    $hasValue = true;
                }

                if ($hasValue) {
                    /** @var TSource $value */
                    return $value;
                }
            }
        }
        throw Errors::noElements();
    }

    /**
     * @template TDefault
     * @param ?callable(TSource):bool $predicate
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function singleOrDefault(?callable $predicate = null, mixed $defaultValue = null): mixed
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate !== null) {
            $value = null;
            $hasValue = false;

            foreach ($source as $element) {
                if ($predicate($element)) {
                    if ($hasValue) {
                        return $defaultValue;
                    }
                    $value = $element;
                    $hasValue = true;
                }
            }

            if ($hasValue) {
                return $value;
            }
        } else {
            if (is_array($source)) {
                switch (count($source)) {
                    case 0:
                        return $defaultValue;
                    case 1:
                        return reset($source);
                    default:
                        return $defaultValue;
                }
            } else {
                $value = null;
                $hasValue = false;

                foreach ($source as $element) {
                    if ($hasValue) {
                        return $defaultValue;
                    }
                    $value = $element;
                    $hasValue = true;
                }

                if ($hasValue) {
                    return $value;
                }
            }
        }
        return $defaultValue;
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function skip(int $count): EnumerableInterface
    {
        if ($count <= 0) {
            return $this;
        }
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new SkipIterator($source, $count);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function skipLast(int $count): EnumerableInterface
    {
        if ($count <= 0) {
            return $this;
        }
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new SkipLastIterator($source, $count);
    }

    /**
     * @param callable(TSource,array-key):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function skipWhile(callable $predicate): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new SkipWhileIterator($source, $predicate);
    }

    /**
     * @param TSource $elements
     * @return EnumerableInterface<TSource>
     */
    public function startWith(mixed ...$elements): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new StartWithIterator($source, $elements);
    }

    /**
     * @param ?callable(TSource):numeric $selector
     */
    public function sum(?callable $selector = null): float|int
    {
        /** @var callable(TSource):numeric */
        $selector = $selector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        $sum = 0;
        foreach ($source as $element) {
            $sum += $selector($element);
        }
        return $sum;
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function take(int $count): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new TakeIterator($source, $count);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function takeLast(int $count): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new TakeLastIterator($source, $count);
    }

    /**
     * @param callable(TSource,array-key):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function takeWhile(callable $predicate): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new TakeWhileIterator($source, $predicate);
    }

    /**
     * @return TSource[]
     */
    public function toArray(): array
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return Converters::toArray($source);
    }

    /**
     * @template TElement
     * @param callable(TSource):array-key $keySelector
     * @param ?callable(TSource):TElement $elementSelector
     * @return array<array-key,TElement>
     */
    public function toDictionary(callable $keySelector, ?callable $elementSelector = null): array
    {
        /** @var callable(TSource):TElement */
        $elementSelector = $elementSelector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return Converters::toDictionary($source, $keySelector, $elementSelector);
    }

    /**
     * @template TElement
     * @param callable(TSource):array-key $keySelector
     * @param ?callable(TSource):TElement $elementSelector
     * @return array<array-key,TElement[]>
     */
    public function toLookup(callable $keySelector, ?callable $elementSelector = null): array
    {
        /** @var callable(TSource):TElement */
        $elementSelector = $elementSelector ?? IdentityFunction::get();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return Converters::toLookup($source, $keySelector, $elementSelector);
    }

    /**
     * @return \Iterator<TSource>
     */
    public function toIterator(): \Iterator
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return Converters::toIterator($source);
    }

    /**
     * @param iterable<TSource> $second
     * @param ?EqualityComparerInterface<TSource> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function union(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        /** @var EqualityComparer<TSource> */
        $comparer = $comparer ?? EqualityComparer::getInstance();
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new UnionIterator($source, $second, $comparer);
    }

    /**
     * @param callable(TSource,array-key):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function where(callable $predicate): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new WhereIterator($source, $predicate);
    }

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<TSource>
     */
    public function while(callable $condition): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new WhileIterator($source, $condition);
    }

    /**
     * @template TSecond
     * @template TResult
     * @param iterable<TSecond> $second
     * @param callable(TSource,TSecond):TResult $resultSelector
     * @return EnumerableInterface<TResult>
     */
    public function zip(iterable $second, callable $resultSelector): EnumerableInterface
    {
        /** @var iterable<TSource> */
        $source = $this->getSource();
        return new ZipIterator($source, $second, $resultSelector);
    }

    /**
     * @return iterable<TSource>
     */
    public function getSource(): iterable
    {
        return $this;
    }
}
