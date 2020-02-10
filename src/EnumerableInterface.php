<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Exception\MoreThanOneElementException;
use Emonkak\Enumerable\Exception\NoSuchElementException;

/**
 * @template TSource
 * @extends \Traversable<TSource>
 */
interface EnumerableInterface extends \Traversable
{
    /**
     * @template TResult
     * @param TResult $seed
     * @param callable(TResult,TSource):TResult $func
     * @return TResult
     */
    public function aggregate($seed, callable $func);

    /**
     * @param callable(TSource):bool|null $predicate
     */
    public function all(?callable $predicate = null): bool;

    /**
     * @param callable(TSource):bool|null $predicate
     * @return bool
     */
    public function any(?callable $predicate = null): bool;

    /**
     * @param callable(TSource):(int|float)|null $selector
     * @return int|float
     * @throws NoSuchElementException
     */
    public function average(?callable $selector = null);

    /**
     * @return EnumerableInterface<TSource[]>
     */
    public function buffer(int $count, ?int $skip = null): EnumerableInterface;

    /**
     * @param callable(\Exception):(iterable<TSource>) $handler
     * @return EnumerableInterface<TSource>
     */
    public function catch(callable $handler): EnumerableInterface;

    /**
     * @param iterable<TSource> ...$sources
     * @return EnumerableInterface<TSource>
     */
    public function concat(iterable ...$sources): EnumerableInterface;

    /**
     * @param callable(TSource):bool|null $predicate
     * @return int
     */
    public function count(?callable $predicate = null): int;

    /**
     * @param TSource $defaultValue
     * @return EnumerableInterface<TSource>
     */
    public function defaultIfEmpty($defaultValue): EnumerableInterface;

    /**
     * @template TKey
     * @param callable(TSource):TKey|null $keySelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function distinct(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @template TKey
     * @param callable(TSource):TKey|null $keySelector
     * @return EnumerableInterface<TSource>
     */
    public function distinctUntilChanged(?callable $keySelector = null): EnumerableInterface;

    /**
     * @param callable(TSource):void $action
     * @return EnumerableInterface<TSource>
     */
    public function do(callable $action): EnumerableInterface;

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<TSource>
     */
    public function doWhile(callable $condition): EnumerableInterface;

    /**
     * @return TSource
     * @throws NoSuchElementException
     */
    public function elementAt(int $index);

    /**
     * @template TDefault
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function elementAtOrDefault(int $index, $defaultValue = null);

    /**
     * @param iterable<TSource> $second
     * @param ?EqualityComparerInterface<TSource> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function except(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @param callable():void $finallyAction
     * @return EnumerableInterface<TSource>
     */
    public function finally(callable $finallyAction): EnumerableInterface;

    /**
     * @param callable(TSource):bool|null $predicate
     * @return TSource
     * @throws NoSuchElementException
     */
    public function first(?callable $predicate = null);

    /**
     * @template TDefault
     * @param callable(TSource):bool|null $predicate
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function firstOrDefault(?callable $predicate = null, $defaultValue = null);

    /**
     * @param callable(TSource):void $action
     */
    public function forEach(callable $action): void;

    /**
     * @template TKey
     * @template TElement
     * @template TResult
     * @param callable(TSource):TKey $keySelector
     * @param callable(TSource):TElement|null $elementSelector
     * @param callable(TKey,TElement[]):TResult|null $resultSelector
     * @param ?EqualityComparerInterface<TKey> $comparer
     * @return EnumerableInterface<TResult>
     */
    public function groupBy(callable $keySelector, ?callable $elementSelector = null, ?callable $resultSelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

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
    public function groupJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @return EnumerableInterface<TSource>
     */
    public function ignoreElements(): EnumerableInterface;

    /**
     * @param iterable<TSource> $second
     * @param ?EqualityComparerInterface<TSource> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function intersect(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    public function isEmpty(): bool;

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
    public function join(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @param callable(TSource):bool|null $predicate
     * @return TSource
     * @throws NoSuchElementException
     */
    public function last(?callable $predicate = null);

    /**
     * @template TDefault
     * @param callable(TSource):bool|null $predicate
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function lastOrDefault(?callable $predicate = null, $defaultValue = null);

    /**
     * @template TKey
     * @param callable(TSource):TKey $selector
     * @return TKey|null
     */
    public function max(?callable $selector = null);

    /**
     * @template TKey
     * @param callable(TSource):TKey $keySelector
     * @return TSource[]
     */
    public function maxBy(callable $keySelector): array;

    /**
     * @return EnumerableInterface<TSource>
     */
    public function memoize(): EnumerableInterface;

    /**
     * @template TKey
     * @param callable(TSource):TKey|null $selector
     * @return TKey|null
     */
    public function min(?callable $selector = null);

    /**
     * @template TKey
     * @param callable(TSource):TKey $keySelector
     * @return TSource[]
     */
    public function minBy(callable $keySelector): array;

    /**
     * @param iterable<TSource> ...$sources
     * @return EnumerableInterface<TSource>
     */
    public function onErrorResumeNext(iterable ...$sources): EnumerableInterface;

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
    public function outerJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @template TKey
     * @param callable(TSource):TKey|null $keySelector
     * @return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderBy(?callable $keySelector = null): OrderedEnumerableInterface;

    /**
     * @template TKey
     * @param callable(TSource):TKey|null $keySelector
     * @return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderByDescending(?callable $keySelector = null): OrderedEnumerableInterface;

    /**
     * @param ?int $count
     * @return EnumerableInterface<TSource>
     */
    public function repeat(?int $count = null): EnumerableInterface;

    /**
     * @param ?int $retryCount
     * @return EnumerableInterface<TSource>
     */
    public function retry(?int $retryCount = null): EnumerableInterface;

    /**
     * @return EnumerableInterface<TSource>
     */
    public function reverse(): EnumerableInterface;

    /**
     * @template TAccumulate
     * @param TAccumulate $seed
     * @param callable(TAccumulate,TSource):TAccumulate $func
     * @return EnumerableInterface<TAccumulate>
     */
    public function scan($seed, callable $func);

    /**
     * @template TResult
     * @param callable(TSource):TResult $selector
     * @return EnumerableInterface<TResult>
     */
    public function select(callable $selector): EnumerableInterface;

    /**
     * @template TResult
     * @param callable(TSource):(iterable<TResult>) $collectionSelector
     * @return EnumerableInterface<TResult>
     */
    public function selectMany(callable $collectionSelector): EnumerableInterface;

    /**
     * @param callable(TSource):bool|null $predicate
     * @return TSource
     * @throws NoSuchElementException
     * @throws MoreThanOneElementException
     */
    public function single(?callable $predicate = null);

    /**
     * @template TDefault
     * @param callable(TSource):bool|null $predicate
     * @param TDefault $defaultValue
     * @return TSource|TDefault
     */
    public function singleOrDefault(?callable $predicate = null, $defaultValue = null);

    /**
     * @return EnumerableInterface<TSource>
     */
    public function skip(int $count): EnumerableInterface;

    /**
     * @return EnumerableInterface<TSource>
     */
    public function skipLast(int $count): EnumerableInterface;

    /**
     * @param callable(TSource):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function skipWhile(callable $predicate): EnumerableInterface;

    /**
     * @param TSource ...$elements
     * @return EnumerableInterface<TSource>
     */
    public function startWith(...$elements): EnumerableInterface;

    /**
     * @param callable(TSource):(int|float)|null $selector
     * @return int|float
     */
    public function sum(?callable $selector = null);

    /**
     * @return EnumerableInterface<TSource>
     */
    public function take(int $count): EnumerableInterface;

    /**
     * @return EnumerableInterface<TSource>
     */
    public function takeLast(int $count): EnumerableInterface;

    /**
     * @param callable(TSource):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function takeWhile(callable $predicate): EnumerableInterface;

    /**
     * @return TSource[]
     */
    public function toArray(): array;

    /**
     * @template TElement
     * @param callable(TSource):array-key $keySelector
     * @param callable(TSource):TElement|null $elementSelector
     * @return array<array-key,TElement>
     */
    public function toDictionary(callable $keySelector, ?callable $elementSelector = null): array;

    /**
     * @template TElement
     * @param callable(TSource):array-key $keySelector
     * @param callable(TSource):TElement|null $elementSelector
     * @return array<array-key,TElement[]>
     */
    public function toLookup(callable $keySelector, ?callable $elementSelector = null): array;

    /**
     * @return \Iterator<TSource>
     */
    public function toIterator(): \Iterator;

    /**
     * @param iterable<TSource> $second
     * @param ?EqualityComparerInterface<TSource> $comparer
     * @return EnumerableInterface<TSource>
     */
    public function union(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @param callable(TSource):bool $predicate
     * @return EnumerableInterface<TSource>
     */
    public function where(callable $predicate): EnumerableInterface;

    /**
     * @param callable():bool $condition
     * @return EnumerableInterface<TSource>
     */
    public function while(callable $condition): EnumerableInterface;

    /**
     * @template TSecond
     * @template TResult
     * @param iterable<TSecond> $second
     * @param callable(TSource,TSecond):TResult $resultSelector
     * @return EnumerableInterface<TResult>
     */
    public function zip(iterable $second, callable $resultSelector): EnumerableInterface;
}
