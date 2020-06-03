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
     * @psalm-param TResult $seed
     * @psalm-param callable(TResult,TSource):TResult $func
     * @psalm-return TResult
     */
    public function aggregate($seed, callable $func)
    {
        $result = $seed;
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            $result = $func($result, $element);
        }
        return $result;
    }

    /**
     * @psalm-param ?callable(TSource):bool $predicate
     */
    public function all(?callable $predicate = null): bool
    {
        $predicate = $predicate ?: [IdentityFunction::class, 'apply'];
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            if (!$predicate($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-return bool
     */
    public function any(?callable $predicate = null): bool
    {
        $predicate = $predicate ?: [IdentityFunction::class, 'apply'];
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            if ($predicate($element)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @psalm-param ?callable(TSource):(int|float) $selector
     * @psalm-return int|float
     * @throws NoSuchElementException
     */
    public function average(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        /** @psalm-var iterable<TSource> */
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
     * @psalm-return EnumerableInterface<TSource[]>
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
     * @psalm-param callable(\Exception):(iterable<TSource>) $handler
     * @psalm-return EnumerableInterface<TSource>
     */
    public function catch(callable $handler): EnumerableInterface
    {
        return new CatchIterator($this->getSource(), $handler);
    }

    /**
     * @psalm-param iterable<TSource> ...$sources
     * @psalm-return EnumerableInterface<TSource>
     */
    public function concat(iterable ...$sources): EnumerableInterface
    {
        return new ConcatIterator(array_merge([$this->getSource()], $sources));
    }

    /**
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-return int
     */
    public function count(?callable $predicate = null): int
    {
        /** @psalm-var iterable<TSource> */
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
     * @psalm-param TSource $defaultValue
     * @psalm-return EnumerableInterface<TSource>
     */
    public function defaultIfEmpty($defaultValue): EnumerableInterface
    {
        return new DefaultIfEmptyIterator($this->getSource(), $defaultValue);
    }

    /**
     * @template TKey
     * @psalm-param ?callable(TSource):TKey $keySelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function distinct(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new DistinctIterator($this->getSource(), $keySelector, $comparer);
    }

    /**
     * @template TKey
     * @psalm-param ?callable(TSource):TKey $keySelector
     * @psalm-return EnumerableInterface<TSource>
     */
    public function distinctUntilChanged(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new DistinctUntilChangedIterator($this->getSource(), $keySelector, $comparer);
    }

    /**
     * @psalm-param callable(TSource):void $action
     * @psalm-return EnumerableInterface<TSource>
     */
    public function do(callable $action): EnumerableInterface
    {
        return new DoIterator($this->getSource(), $action);
    }

    /**
     * @psalm-param callable():bool $condition
     * @psalm-return EnumerableInterface<TSource>
     */
    public function doWhile(callable $condition): EnumerableInterface
    {
        return new DoWhileIterator($this->getSource(), $condition);
    }

    /**
     * @psalm-return TSource
     * @throws NoSuchElementException
     */
    public function elementAt(int $index)
    {
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        if (is_array($source)) {
            if (isset($source[$index])) {
                /** @psalm-var TSource */
                $element = $source[$index];
                return $element;
            }
        } elseif ($source instanceof \SeekableIterator) {
            $source->seek($index);
            if ($source->valid()) {
                /** @psalm-var TSource */
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
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function elementAtOrDefault(int $index, $defaultValue = null)
    {
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        if (is_array($source)) {
            if (isset($source[$index])) {
                /** @psalm-var TSource */
                $element = $source[$index];
                return $element;
            }
        } elseif ($source instanceof \SeekableIterator) {
            $source->seek($index);
            if ($source->valid()) {
                /** @psalm-var TSource */
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
     * @psalm-param iterable<TSource> $second
     * @psalm-param ?EqualityComparerInterface<TSource> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function except(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new ExceptIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @psalm-param callable():void $finallyAction
     * @psalm-return EnumerableInterface<TSource>
     */
    public function finally(callable $finallyAction): EnumerableInterface
    {
        return new FinallyIterator($this->getSource(), $finallyAction);
    }

    /**
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-return TSource
     * @throws NoSuchElementException
     */
    public function first(?callable $predicate = null)
    {
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate) {
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
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function firstOrDefault(?callable $predicate = null, $defaultValue = null)
    {
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate) {
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
     * @psalm-param callable(TSource):void $action
     */
    public function forEach(callable $action): void
    {
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        foreach ($source as $element) {
            $action($element);
        }
    }

    /**
     * @template TKey
     * @template TElement
     * @template TResult
     * @psalm-param callable(TSource):TKey $keySelector
     * @psalm-param ?callable(TSource):TElement $elementSelector
     * @psalm-param ?callable(TKey,TElement[]):TResult $resultSelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TResult>
     */
    public function groupBy(callable $keySelector, ?callable $elementSelector = null, ?callable $resultSelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        $resultSelector = $resultSelector ?:
            /**
             * @psalm-param TKey $key
             * @psalm-param TElement[] $values
             * @psalm-return array{0:TKey,1:TElement[]}
             */
            static function($key, array $values): array {
                return [$key, $values];
            };
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new GroupByIterator($this->getSource(), $keySelector, $elementSelector, $resultSelector, $comparer);
    }

    /**
     * @template TInner
     * @template TKey
     * @template TResult
     * @psalm-param iterable<TInner> $inner
     * @psalm-param callable(TSource):TKey $outerKeySelector
     * @psalm-param callable(TInner):TKey $innerKeySelector
     * @psalm-param callable(TSource,TInner[]):TResult $resultSelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TResult>
     */
    public function groupJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new GroupJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function ignoreElements(): EnumerableInterface
    {
        return new EmptyIterator();
    }

    /**
     * @psalm-param iterable<TSource> $second
     * @psalm-param ?EqualityComparerInterface<TSource> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function intersect(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new IntersectIterator($this->getSource(), $second, $comparer);
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
     * @psalm-param iterable<TInner> $inner
     * @psalm-param callable(TSource):TKey $outerKeySelector
     * @psalm-param callable(TInner):TKey $innerKeySelector
     * @psalm-param callable(TSource,TInner):TResult $resultSelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TResult>
     */
    public function join(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new JoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-return TSource
     * @throws NoSuchElementException
     */
    public function last(?callable $predicate = null)
    {
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate) {
            $hasValue = false;
            $value = null;
            foreach ($source as $element) {
                if ($predicate($element)) {
                    $value = $element;
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                /** @psalm-var TSource $value */
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
                /** @psalm-var TSource $value */
                return $value;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @template TDefault
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function lastOrDefault(?callable $predicate = null, $defaultValue = null)
    {
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        if ($predicate) {
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
     * @template TKey
     * @psalm-param callable(TSource):TKey $selector
     * @psalm-return ?TKey
     */
    public function max(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        /** @psalm-var iterable<TSource> */
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
     * @template TKey
     * @psalm-param callable(TSource):TKey $keySelector
     * @psalm-return EnumerableInterface<TSource>
     */
    public function maxBy(callable $keySelector): EnumerableInterface
    {
        /** @psalm-var \Iterator<TSource> */
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
     * @psalm-return EnumerableInterface<TSource>
     */
    public function memoize(): EnumerableInterface
    {
        return new MemoizeIterator($this->toIterator());
    }

    /**
     * @template TKey
     * @psalm-param ?callable(TSource):TKey $selector
     * @psalm-return ?TKey
     */
    public function min(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        /** @psalm-var iterable<TSource> */
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
     * @template TKey
     * @psalm-param callable(TSource):TKey $keySelector
     * @psalm-return EnumerableInterface<TSource>
     */
    public function minBy(callable $keySelector): EnumerableInterface
    {
        /** @psalm-var \Iterator<TSource> */
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
     * @psalm-param iterable<TSource> ...$sources
     * @psalm-return EnumerableInterface<TSource>
     */
    public function onErrorResumeNext(iterable ...$sources): EnumerableInterface
    {
        return new OnErrorResumeNextIterator(array_merge([$this->getSource()], $sources));
    }

    /**
     * @template TInner
     * @template TKey
     * @template TResult
     * @psalm-param iterable<TInner> $inner
     * @psalm-param callable(TSource):TKey $outerKeySelector
     * @psalm-param callable(TInner):TKey $innerKeySelector
     * @psalm-param callable(TSource,?TInner):TResult $resultSelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TResult>
     */
    public function outerJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new OuterJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @template TKey
     * @psalm-param ?callable(TSource):TKey $keySelector
     * @psalm-return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderBy(?callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        return new OrderByIterator($this->getSource(), $keySelector, false);
    }

    /**
     * @template TKey
     * @psalm-param ?callable(TSource):TKey $keySelector
     * @psalm-return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderByDescending(?callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        return new OrderByIterator($this->getSource(), $keySelector, true);
    }

    /**
     * @psalm-param ?int $count
     * @psalm-return EnumerableInterface<TSource>
     */
    public function repeat(?int $count = null): EnumerableInterface
    {
        return new RepeatIterator($this->getSource(), $count);
    }

    /**
     * @psalm-param ?int $retryCount
     * @psalm-return EnumerableInterface<TSource>
     */
    public function retry(?int $retryCount = null): EnumerableInterface
    {
        return new RetryIterator($this->getSource(), $retryCount);
    }

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function reverse(): EnumerableInterface
    {
        return new ReverseIterator($this->getSource());
    }

    /**
     * @template TAccumulate
     * @psalm-param TAccumulate $seed
     * @psalm-param callable(TAccumulate,TSource):TAccumulate $func
     * @psalm-return EnumerableInterface<TAccumulate>
     */
    public function scan($seed, callable $func): EnumerableInterface
    {
        return new ScanIterator($this->getSource(), $seed, $func);
    }

    /**
     * @template TResult
     * @psalm-param callable(TSource,array-key):TResult $selector
     * @psalm-return EnumerableInterface<TResult>
     */
    public function select(callable $selector): EnumerableInterface
    {
        return new SelectIterator($this->getSource(), $selector);
    }

    /**
     * @template TResult
     * @psalm-param callable(TSource,array-key):(iterable<TResult>) $collectionSelector
     * @psalm-return EnumerableInterface<TResult>
     */
    public function selectMany(callable $collectionSelector): EnumerableInterface
    {
        return new SelectManyIterator($this->getSource(), $collectionSelector);
    }

    /**
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-return TSource
     * @throws NoSuchElementException
     * @throws MoreThanOneElementException
     */
    public function single(?callable $predicate = null)
    {
        /** @psalm-var iterable<TSource> */
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
                /** @psalm-var TSource $value */
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
                    /** @psalm-var TSource $value */
                    return $value;
                }
            }
        }
        throw Errors::noElements();
    }

    /**
     * @template TDefault
     * @psalm-param ?callable(TSource):bool $predicate
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function singleOrDefault(?callable $predicate = null, $defaultValue = null)
    {
        /** @psalm-var iterable<TSource> */
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
     * @psalm-return EnumerableInterface<TSource>
     */
    public function skip(int $count): EnumerableInterface
    {
        if ($count <= 0) {
            return $this;
        }
        return new SkipIterator($this->getSource(), $count);
    }

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function skipLast(int $count): EnumerableInterface
    {
        if ($count <= 0) {
            return $this;
        }
        return new SkipLastIterator($this->getSource(), $count);
    }

    /**
     * @psalm-param callable(TSource,array-key):bool $predicate
     * @psalm-return EnumerableInterface<TSource>
     */
    public function skipWhile(callable $predicate): EnumerableInterface
    {
        return new SkipWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @psalm-param TSource ...$elements
     * @psalm-return EnumerableInterface<TSource>
     */
    public function startWith(...$elements): EnumerableInterface
    {
        return new StartWithIterator($this->getSource(), $elements);
    }

    /**
     * @psalm-param ?callable(TSource):(int|float) $selector
     * @psalm-return int|float
     */
    public function sum(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        /** @psalm-var iterable<TSource> */
        $source = $this->getSource();
        $sum = 0;
        foreach ($source as $element) {
            $sum += $selector($element);
        }
        return $sum;
    }

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function take(int $count): EnumerableInterface
    {
        return new TakeIterator($this->getSource(), $count);
    }

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function takeLast(int $count): EnumerableInterface
    {
        return new TakeLastIterator($this->getSource(), $count);
    }

    /**
     * @psalm-param callable(TSource,array-key):bool $predicate
     * @psalm-return EnumerableInterface<TSource>
     */
    public function takeWhile(callable $predicate): EnumerableInterface
    {
        return new TakeWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @psalm-return TSource[]
     */
    public function toArray(): array
    {
        return Converters::toArray($this->getSource());
    }

    /**
     * @template TElement
     * @psalm-param callable(TSource):array-key $keySelector
     * @psalm-param ?callable(TSource):TElement $elementSelector
     * @psalm-return array<array-key,TElement>
     */
    public function toDictionary(callable $keySelector, ?callable $elementSelector = null): array
    {
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        return Converters::toDictionary($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @template TElement
     * @psalm-param callable(TSource):array-key $keySelector
     * @psalm-param ?callable(TSource):TElement $elementSelector
     * @psalm-return array<array-key,TElement[]>
     */
    public function toLookup(callable $keySelector, ?callable $elementSelector = null): array
    {
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        return Converters::toLookup($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @psalm-return \Iterator<TSource>
     */
    public function toIterator(): \Iterator
    {
        return Converters::toIterator($this->getSource());
    }

    /**
     * @psalm-param iterable<TSource> $second
     * @psalm-param ?EqualityComparerInterface<TSource> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function union(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new UnionIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @psalm-param callable(TSource,array-key):bool $predicate
     * @psalm-return EnumerableInterface<TSource>
     */
    public function where(callable $predicate): EnumerableInterface
    {
        return new WhereIterator($this->getSource(), $predicate);
    }

    /**
     * @psalm-param callable():bool $condition
     * @psalm-return EnumerableInterface<TSource>
     */
    public function while(callable $condition): EnumerableInterface
    {
        return new WhileIterator($this->getSource(), $condition);
    }

    /**
     * @template TSecond
     * @template TResult
     * @psalm-param iterable<TSecond> $second
     * @psalm-param callable(TSource,TSecond):TResult $resultSelector
     * @psalm-return EnumerableInterface<TResult>
     */
    public function zip(iterable $second, callable $resultSelector): EnumerableInterface
    {
        return new ZipIterator($this->getSource(), $second, $resultSelector);
    }

    /**
     * @psalm-return iterable<TSource>
     */
    public function getSource(): iterable
    {
        return $this;
    }
}
