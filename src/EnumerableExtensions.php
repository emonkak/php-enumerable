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
    public function aggregate($seed, callable $func)
    {
        $result = $seed;
        foreach ($this->getSource() as $element) {
            $result = $func($result, $element);
        }
        return $result;
    }

    /**
     * @param ?callable(TSource):bool $predicate
     */
    public function all(?callable $predicate = null): bool
    {
        $predicate = $predicate ?: [IdentityFunction::class, 'apply'];
        foreach ($this->getSource() as $element) {
            if (!$predicate($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return bool
     */
    public function any(?callable $predicate = null): bool
    {
        $predicate = $predicate ?: [IdentityFunction::class, 'apply'];
        foreach ($this->getSource() as $element) {
            if ($predicate($element)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ?callable(TSource):(int|float) $selector
     * @return int|float
     * @throws NoSuchElementException
     */
    public function average(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        $sum = 0;
        $count = 0;
        foreach ($this->getSource() as $element) {
            $sum += $selector($element);
            $count++;
        }
        if ($count === 0) {
            throw Errors::noElements();
        }
        return $sum / $count;
    }

    /**
     * @return EnumerableInterface<TSource>
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
     * @param callable(\Exception):(iterable<TSource>) $handler
     * @return EnumerableInterface<TSource>
     */
    public function catch(callable $handler): EnumerableInterface
    {
        return new CatchIterator($this->getSource(), $handler);
    }

    /**
     * @param iterable<TSource> ...$sources
     * @return EnumerableInterface<TSource>
     */
    public function concat(iterable ...$sources): EnumerableInterface
    {
        return new ConcatIterator(array_merge([$this->getSource()], $sources));
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return int
     */
    public function count(?callable $predicate = null): int
    {
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
            // @phan-suppress-next-line PhanTypeMismatchArgumentInternal
            return iterator_count($source);
        }
    }

    /**
     * @param TSource $defaultValue
     * @return EnumerableInterface<TSource>
     */
    public function defaultIfEmpty($defaultValue): EnumerableInterface
    {
        return new DefaultIfEmptyIterator($this->getSource(), $defaultValue);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function distinct(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new DistinctIterator($this->getSource(), $keySelector, $comparer);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @param ?EqualityComparerInterface $comparer
     * @return EnumerableInterface<TSource>
     */
    public function distinctUntilChanged(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new DistinctUntilChangedIterator($this->getSource(), $keySelector, $comparer);
    }

    /**
     * @param callable(TSource):void $action
     * @return EnumerableInterface<TSource>
     */
    public function do(callable $action): EnumerableInterface
    {
        return new DoIterator($this->getSource(), $action);
    }

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<TSource>
     */
    public function doWhile(callable $condition): EnumerableInterface
    {
        return new DoWhileIterator($this->getSource(), $condition);
    }

    /**
     * @return TSource
     * @throws NoSuchElementException
     */
    public function elementAt(int $index)
    {
        $source = $this->getSource();
        if (is_array($source) && isset($source[0])) {
            if ($index < count($source)) {
                return $source[$index];
            }
        } else {
            foreach ($source as $element) {
                if ($index === 0) {
                    return $element;
                }
                $index--;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @template TDefault
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function elementAtOrDefault(int $index, $defaultValue = null)
    {
        $source = $this->getSource();
        if (is_array($source) && isset($source[0])) {
            if ($index < count($source)) {
                return $source[$index];
            }
        } else {
            foreach ($source as $element) {
                if ($index === 0) {
                    return $element;
                }
                $index--;
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
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new ExceptIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @param callable():void $finallyAction
     * @return EnumerableInterface<TSource>
     */
    public function finally(callable $finallyAction): EnumerableInterface
    {
        return new FinallyIterator($this->getSource(), $finallyAction);
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return TSource
     * @throws NoSuchElementException
     */
    public function first(?callable $predicate = null)
    {
        if ($predicate) {
            foreach ($this->getSource() as $element) {
                if ($predicate($element)) {
                    return $element;
                }
            }
        } else {
            foreach ($this->getSource() as $element) {
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
    public function firstOrDefault(?callable $predicate = null, $defaultValue = null)
    {
        if ($predicate) {
            foreach ($this->getSource() as $element) {
                if ($predicate($element)) {
                    return $element;
                }
            }
        } else {
            foreach ($this->getSource() as $element) {
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
        foreach ($this->getSource() as $element) {
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
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        $resultSelector = $resultSelector ?: function($k, $vs) {
            return [$k, $vs];
        };
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new GroupByIterator($this->getSource(), $keySelector, $elementSelector, $resultSelector, $comparer);
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
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new GroupJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
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
     * @param iterable<TInner> $inner
     * @param callable(TSource):TKey $outerKeySelector
     * @param callable(TInner):TKey $innerKeySelector
     * @param callable(TSource,TInner):TResult $resultSelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TResult>
     */
    public function join(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new JoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return TSource
     * @throws NoSuchElementException
     */
    public function last(?callable $predicate = null)
    {
        if ($predicate) {
            $hasValue = false;
            $value = null;
            foreach ($this->getSource() as $element) {
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
            foreach ($this->getSource() as $element) {
                $hasValue = true;
                $value = $element;
            }
            if ($hasValue) {
                return $element;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return TSource
     */
    public function lastOrDefault(?callable $predicate = null, $defaultValue = null)
    {
        if ($predicate) {
            $hasValue = false;
            $value = null;
            foreach ($this->getSource() as $element) {
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
            foreach ($this->getSource() as $element) {
                $hasValue = true;
                $value = $element;
            }
            if ($hasValue) {
                return $element;
            }
        }
        return $defaultValue;
    }

    /**
     * @param ?callable(TSource):(int|float) $selector
     * @return int|float
     */
    public function max(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        $max = -INF;
        foreach ($this->getSource() as $element) {
            $value = $selector($element);
            if ($max < $value) {
                $max = $value;
            }
        }
        return $max;
    }

    /**
     * @template TKey
     * @param callable(TSource):TKey $keySelector
     * @return TSource[]
     */
    public function maxBy(callable $keySelector): array
    {
        $result = [];

        $iterator = $this->toIterator();
        $iterator->rewind();

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

        return $result;
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function memoize(): EnumerableInterface
    {
        return new MemoizeIterator($this->toIterator());
    }

    /**
     * @param ?callable(TSource):(int|float) $selector
     * @return int|float
     */
    public function min(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        $max = INF;
        foreach ($this->getSource() as $element) {
            $value = $selector($element);
            if ($max > $value) {
                $max = $value;
            }
        }
        return $max;
    }

    /**
     * @template TKey
     * @param callable(TSource):TKey $keySelector
     * @return TSource[]
     */
    public function minBy(callable $keySelector): array
    {
        $result = [];

        $iterator = $this->toIterator();
        $iterator->rewind();

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

        return $result;
    }

    /**
     * @param iterable<TSource> ...$sources
     * @return EnumerableInterface<TSource>
     */
    public function onErrorResumeNext(iterable ...$sources): EnumerableInterface
    {
        return new OnErrorResumeNextIterator(array_merge([$this->getSource()], $sources));
    }

    /**
     * @template TInner
     * @template TKey
     * @template TResult
     * @param iterable<TInner> $inner
     * @param callable(TSource):TKey $outerKeySelector
     * @param callable(TInner):TKey $innerKeySelector
     * @param callable(TSource,TInner|null):TResult $resultSelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TResult>
     */
    public function outerJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new OuterJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderBy(?callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        return new OrderByIterator($this->getSource(), $keySelector, false);
    }

    /**
     * @template TKey
     * @param ?callable(TSource):TKey $keySelector
     * @return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderByDescending(?callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        return new OrderByIterator($this->getSource(), $keySelector, true);
    }

    /**
     * @param ?int $count
     * @return EnumerableInterface<TSource>
     */
    public function repeat(?int $count = null): EnumerableInterface
    {
        return new RepeatIterator($this->getSource(), $count);
    }

    /**
     * @param ?int $retryCount
     * @return EnumerableInterface<TSource>
     */
    public function retry(?int $retryCount = null): EnumerableInterface
    {
        return new RetryIterator($this->getSource(), $retryCount);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function reverse(): EnumerableInterface
    {
        return new ReverseIterator($this->getSource());
    }

    /**
     * @template TAccumulate
     * @param TAccumulate $seed
     * @param callable(TAccumulate,TSource):TAccumulate $func
     * @return EnumerableInterface<TAccumulate>
     */
    public function scan($seed, callable $func): EnumerableInterface
    {
        return new ScanIterator($this->getSource(), $seed, $func);
    }

    /**
     * @template TResult
     * @param callable(TSource):TResult $selector
     * @return EnumerableInterface<TResult>
     */
    public function select(callable $selector): EnumerableInterface
    {
        return new SelectIterator($this->getSource(), $selector);
    }

    /**
     * @template TResult
     * @param callable(TSource):(iterable<TResult>) $collectionSelector
     * @return EnumerableInterface<TResult>
     */
    public function selectMany(callable $collectionSelector): EnumerableInterface
    {
        return new SelectManyIterator($this->getSource(), $collectionSelector);
    }

    /**
     * @param ?callable(TSource):bool $predicate
     * @return TSource
     * @throws NoSuchElementException
     * @throws MoreThanOneElementException
     */
    public function single(?callable $predicate = null)
    {
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
    public function singleOrDefault(?callable $predicate = null, $defaultValue = null)
    {
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
            // @phan-suppress-next-line PhanTypeMismatchReturn
            return $this;
        }
        return new SkipIterator($this->getSource(), $count);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function skipLast(int $count): EnumerableInterface
    {
        if ($count <= 0) {
            // @phan-suppress-next-line PhanTypeMismatchReturn
            return $this;
        }
        return new SkipLastIterator($this->getSource(), $count);
    }

    /**
     * @param callable(TSource):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function skipWhile(callable $predicate): EnumerableInterface
    {
        return new SkipWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @param TSource ...$elements
     * @return EnumerableInterface<TSource>
     */
    public function startWith(...$elements): EnumerableInterface
    {
        return new StartWithIterator($this->getSource(), $elements);
    }

    /**
     * @param ?callable(TSource):(int|float) $selector
     * @return int|float
     */
    public function sum(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        $sum = 0;
        foreach ($this->getSource() as $element) {
            $sum += $selector($element);
        }
        return $sum;
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function take(int $count): EnumerableInterface
    {
        return new TakeIterator($this->getSource(), $count);
    }

    /**
     * @return EnumerableInterface<TSource>
     */
    public function takeLast(int $count): EnumerableInterface
    {
        return new TakeLastIterator($this->getSource(), $count);
    }

    /**
     * @param callable(TSource):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function takeWhile(callable $predicate): EnumerableInterface
    {
        return new TakeWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @return TSource[]
     */
    public function toArray(): array
    {
        return Converters::toArray($this->getSource());
    }

    /**
     * @template TElement
     * @param callable(TSource):string $keySelector
     * @param ?callable(TSource):TElement $elementSelector
     * @return array<string,TElement>
     */
    public function toDictionary(callable $keySelector, ?callable $elementSelector = null): array
    {
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        return Converters::toDictionary($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @template TElement
     * @param callable(TSource):string $keySelector
     * @param ?callable(TSource):TElement $elementSelector
     * @return array<string,TElement[]>
     */
    public function toLookup(callable $keySelector, ?callable $elementSelector = null): array
    {
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        return Converters::toLookup($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @return \Iterator<TSource>
     */
    public function toIterator(): \Iterator
    {
        return Converters::toIterator($this->getSource());
    }

    /**
     * @param iterable<TSource> $second
     * @param ?EqualityComparerInterface<TSource> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function union(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new UnionIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @param callable(TSource):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function where(callable $predicate): EnumerableInterface
    {
        return new WhereIterator($this->getSource(), $predicate);
    }

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<TSource>
     */
    public function while(callable $condition): EnumerableInterface
    {
        return new WhileIterator($this->getSource(), $condition);
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
        return new ZipIterator($this->getSource(), $second, $resultSelector);
    }

    /**
     * @return iterable<TSource>
     */
    public function getSource(): iterable
    {
        // @phan-suppress-next-line PhanTypeMismatchReturn
        return $this;
    }
}
