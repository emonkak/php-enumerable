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

trait EnumerableExtensions
{
    /**
     * @param mixed $seed
     * @param callable(mixed,mixed):mixed $func
     * @return mixed
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
     * @param callable(mixed):bool $predicate
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
     * @param callable(mixed):bool|null $predicate
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
     * @param callable(mixed):(int|float)|null $selector
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
     * @return EnumerableInterface<mixed>
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
     * @param callable(\Exception):(iterable<mixed>) $handler
     * @return EnumerableInterface<mixed>
     */
    public function catch(callable $handler): EnumerableInterface
    {
        return new CatchIterator($this->getSource(), $handler);
    }

    /**
     * @param iterable<mixed> ...$sources
     * @return EnumerableInterface<mixed>
     */
    public function concat(iterable ...$sources): EnumerableInterface
    {
        return new ConcatIterator(array_merge([$this->getSource()], $sources));
    }

    /**
     * @param callable(mixed):bool|null $predicate
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
            return iterator_count($source);
        }
    }

    /**
     * @param mixed $defaultValue
     * @return EnumerableInterface<mixed>
     */
    public function defaultIfEmpty($defaultValue): EnumerableInterface
    {
        return new DefaultIfEmptyIterator($this->getSource(), $defaultValue);
    }

    /**
     * @param callable(mixed):mixed|null $keySelector
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
     */
    public function distinct(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new DistinctIterator($this->getSource(), $keySelector, $comparer);
    }

    /**
     * @param callable(mixed):mixed|null $keySelector
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
     */
    public function distinctUntilChanged(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new DistinctUntilChangedIterator($this->getSource(), $keySelector, $comparer);
    }

    /**
     * @param callable(mixed):void $action
     * @return EnumerableInterface<mixed>
     */
    public function do(callable $action): EnumerableInterface
    {
        return new DoIterator($this->getSource(), $action);
    }

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<mixed>
     */
    public function doWhile(callable $condition): EnumerableInterface
    {
        return new DoWhileIterator($this->getSource(), $condition);
    }

    /**
     * @return mixed
     * @throws NoSuchElementException
     */
    public function elementAt(int $index)
    {
        $source = $this->getSource();
        if (is_array($source) || $source instanceof \ArrayAccess) {
            // @var array|\ArrayAccess $source
            if (isset($source[$index])) {
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
     * @param mixed $defaultValue
     * @return mixed
     */
    public function elementAtOrDefault(int $index, $defaultValue = null)
    {
        $source = $this->getSource();
        if (is_array($source) || $source instanceof \ArrayAccess) {
            // @var array|\ArrayAccess $source
            if (isset($source[$index])) {
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
     * @param iterable<mixed> $second
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
     */
    public function except(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new ExceptIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @param callable():void $finallyAction
     * @return EnumerableInterface<mixed>
     */
    public function finally(callable $finallyAction): EnumerableInterface
    {
        return new FinallyIterator($this->getSource(), $finallyAction);
    }

    /**
     * @param callable(mixed):bool|null $predicate
     * @return mixed
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
     * @param callable(mixed):bool|null $predicate
     * @param mixed $defaultValue
     * @return mixed
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
     * @param callable(mixed):void $action
     */
    public function forEach(callable $action): void
    {
        foreach ($this->getSource() as $element) {
            $action($element);
        }
    }

    /**
     * @param callable(mixed):mixed $keySelector
     * @param callable(mixed):mixed|null $elementSelector
     * @param callable(mixed,mixed[]):mixed|null $resultSelector
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
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
     * @param iterable<mixed> $inner
     * @param callable(mixed):mixed $outerKeySelector
     * @param callable(mixed):mixed $innerKeySelector
     * @param callable(mixed,mixed[]):mixed $resultSelector
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
     */
    public function groupJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new GroupJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @return EnumerableInterface<mixed>
     */
    public function ignoreElements(): EnumerableInterface
    {
        return new EmptyIterator();
    }

    /**
     * @param iterable<mixed> $second
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
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
     * @param iterable<mixed> $inner
     * @param callable(mixed):mixed $outerKeySelector
     * @param callable(mixed):mixed $innerKeySelector
     * @param callable(mixed,mixed):mixed $resultSelector
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
     */
    public function join(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new JoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @param callable(mixed):bool|null $predicate
     * @return mixed
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
                return $value;
            }
        }
        throw Errors::noElements();
    }

    /**
     * @param callable(mixed):bool|null $predicate
     * @param mixed $defaultValue
     * @return mixed|mixed
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
                return $value;
            }
        }
        return $defaultValue;
    }

    /**
     * @param callable(mixed):mixed|null $selector
     * @return mixed
     */
    public function max(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        $max = null;
        foreach ($this->getSource() as $element) {
            $value = $selector($element);
            if ($max === null || $max < $value) {
                $max = $value;
            }
        }
        return $max;
    }

    /**
     * @param callable(mixed):mixed $keySelector
     * @return mixed[]
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
     * @return EnumerableInterface<mixed>
     */
    public function memoize(): EnumerableInterface
    {
        return new MemoizeIterator($this->toIterator());
    }

    /**
     * @param callable(mixed):mixed|null $selector
     * @return mixed
     */
    public function min(?callable $selector = null)
    {
        $selector = $selector ?: [IdentityFunction::class, 'apply'];
        $min = null;
        foreach ($this->getSource() as $element) {
            $value = $selector($element);
            if ($min === null || $min > $value) {
                $min = $value;
            }
        }
        return $min;
    }

    /**
     * @param callable(mixed):mixed $keySelector
     * @return mixed[]
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
     * @param iterable<mixed> ...$sources
     * @return EnumerableInterface<mixed>
     */
    public function onErrorResumeNext(iterable ...$sources): EnumerableInterface
    {
        return new OnErrorResumeNextIterator(array_merge([$this->getSource()], $sources));
    }

    /**
     * @param iterable<mixed> $inner
     * @param callable(mixed):mixed $outerKeySelector
     * @param callable(mixed):mixed $innerKeySelector
     * @param callable(mixed,mixed|null):mixed $resultSelector
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
     */
    public function outerJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new OuterJoinIterator($this->getSource(), $inner, $outerKeySelector, $innerKeySelector, $resultSelector, $comparer);
    }

    /**
     * @param callable(mixed):mixed|null $keySelector
     * @return OrderedEnumerableInterface<mixed,mixed>
     */
    public function orderBy(?callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        return new OrderByIterator($this->getSource(), $keySelector, false);
    }

    /**
     * @param callable(mixed):mixed|null $keySelector
     * @return OrderedEnumerableInterface<mixed,mixed>
     */
    public function orderByDescending(?callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        return new OrderByIterator($this->getSource(), $keySelector, true);
    }

    /**
     * @param ?int $count
     * @return EnumerableInterface<mixed>
     */
    public function repeat(?int $count = null): EnumerableInterface
    {
        return new RepeatIterator($this->getSource(), $count);
    }

    /**
     * @param ?int $retryCount
     * @return EnumerableInterface<mixed>
     */
    public function retry(?int $retryCount = null): EnumerableInterface
    {
        return new RetryIterator($this->getSource(), $retryCount);
    }

    /**
     * @return EnumerableInterface<mixed>
     */
    public function reverse(): EnumerableInterface
    {
        return new ReverseIterator($this->getSource());
    }

    /**
     * @param mixed $seed
     * @param callable(mixed,mixed):mixed $func
     * @return EnumerableInterface<mixed>
     */
    public function scan($seed, callable $func): EnumerableInterface
    {
        return new ScanIterator($this->getSource(), $seed, $func);
    }

    /**
     * @param callable(mixed):mixed $selector
     * @return EnumerableInterface<mixed>
     */
    public function select(callable $selector): EnumerableInterface
    {
        return new SelectIterator($this->getSource(), $selector);
    }

    /**
     * @param callable(mixed):(iterable<mixed>) $collectionSelector
     * @return EnumerableInterface<mixed>
     */
    public function selectMany(callable $collectionSelector): EnumerableInterface
    {
        return new SelectManyIterator($this->getSource(), $collectionSelector);
    }

    /**
     * @param callable(mixed):bool|null $predicate
     * @return mixed
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
     * @param callable(mixed):bool|null $predicate
     * @param mixed $defaultValue
     * @return mixed
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
     * @return EnumerableInterface<mixed>
     */
    public function skip(int $count): EnumerableInterface
    {
        if ($count <= 0) {
            return $this;
        }
        return new SkipIterator($this->getSource(), $count);
    }

    /**
     * @return EnumerableInterface<mixed>
     */
    public function skipLast(int $count): EnumerableInterface
    {
        if ($count <= 0) {
            return $this;
        }
        return new SkipLastIterator($this->getSource(), $count);
    }

    /**
     * @param callable(mixed):bool $predicate
     * @return EnumerableInterface<mixed>
     */
    public function skipWhile(callable $predicate): EnumerableInterface
    {
        return new SkipWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @param mixed ...$elements
     * @return EnumerableInterface<mixed>
     */
    public function startWith(...$elements): EnumerableInterface
    {
        return new StartWithIterator($this->getSource(), $elements);
    }

    /**
     * @param callable(mixed):(int|float)|null $selector
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
     * @return EnumerableInterface<mixed>
     */
    public function take(int $count): EnumerableInterface
    {
        return new TakeIterator($this->getSource(), $count);
    }

    /**
     * @return EnumerableInterface<mixed>
     */
    public function takeLast(int $count): EnumerableInterface
    {
        return new TakeLastIterator($this->getSource(), $count);
    }

    /**
     * @param callable(mixed):bool $predicate
     * @return EnumerableInterface<mixed>
     */
    public function takeWhile(callable $predicate): EnumerableInterface
    {
        return new TakeWhileIterator($this->getSource(), $predicate);
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return Converters::toArray($this->getSource());
    }

    /**
     * @param callable(mixed):array-key $keySelector
     * @param callable(mixed):mixed|null $elementSelector
     * @return array<array-key,mixed>
     */
    public function toDictionary(callable $keySelector, ?callable $elementSelector = null): array
    {
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        return Converters::toDictionary($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @param callable(mixed):array-key $keySelector
     * @param callable(mixed):mixed|null $elementSelector
     * @return array<array-key,mixed[]>
     */
    public function toLookup(callable $keySelector, ?callable $elementSelector = null): array
    {
        $elementSelector = $elementSelector ?: [IdentityFunction::class, 'apply'];
        return Converters::toLookup($this->getSource(), $keySelector, $elementSelector);
    }

    /**
     * @return \Iterator<mixed>
     */
    public function toIterator(): \Iterator
    {
        return Converters::toIterator($this->getSource());
    }

    /**
     * @param iterable<mixed> $second
     * @param ?EqualityComparerInterface<mixed> $comparer
     * @return EnumerableInterface<mixed>
     */
    public function union(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface
    {
        $comparer = $comparer ?: EqualityComparer::getInstance();
        return new UnionIterator($this->getSource(), $second, $comparer);
    }

    /**
     * @param callable(mixed):bool $predicate
     * @return EnumerableInterface<mixed>
     */
    public function where(callable $predicate): EnumerableInterface
    {
        return new WhereIterator($this->getSource(), $predicate);
    }

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<mixed>
     */
    public function while(callable $condition): EnumerableInterface
    {
        return new WhileIterator($this->getSource(), $condition);
    }

    /**
     * @param iterable<mixed> $second
     * @param callable(mixed,mixed):mixed $resultSelector
     * @return EnumerableInterface<mixed>
     */
    public function zip(iterable $second, callable $resultSelector): EnumerableInterface
    {
        return new ZipIterator($this->getSource(), $second, $resultSelector);
    }

    /**
     * @return iterable<mixed>
     */
    public function getSource(): iterable
    {
        return $this;
    }
}
