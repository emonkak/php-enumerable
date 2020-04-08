<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Exception\MoreThanOneElementException;
use Emonkak\Enumerable\Exception\NoSuchElementException;

/**
 * @template TSource
 * @template-extends \Traversable<TSource>
 */
interface EnumerableInterface extends \Traversable
{
    /**
     * @template TResult
     * @psalm-param TResult $seed
     * @psalm-param callable(TResult,TSource):TResult $func
     * @psalm-return TResult
     */
    public function aggregate($seed, callable $func);

    /**
     * @psalm-param callable(TSource):bool|null $predicate
     */
    public function all(?callable $predicate = null): bool;

    /**
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-return bool
     */
    public function any(?callable $predicate = null): bool;

    /**
     * @psalm-param callable(TSource):(int|float)|null $selector
     * @psalm-return int|float
     * @throws NoSuchElementException
     */
    public function average(?callable $selector = null);

    /**
     * @psalm-return EnumerableInterface<TSource[]>
     */
    public function buffer(int $count, ?int $skip = null): EnumerableInterface;

    /**
     * @psalm-param callable(\Exception):(iterable<TSource>) $handler
     * @psalm-return EnumerableInterface<TSource>
     */
    public function catch(callable $handler): EnumerableInterface;

    /**
     * @psalm-param iterable<TSource> ...$sources
     * @psalm-return EnumerableInterface<TSource>
     */
    public function concat(iterable ...$sources): EnumerableInterface;

    /**
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-return int
     */
    public function count(?callable $predicate = null): int;

    /**
     * @psalm-param TSource $defaultValue
     * @psalm-return EnumerableInterface<TSource>
     */
    public function defaultIfEmpty($defaultValue): EnumerableInterface;

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey|null $keySelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function distinct(?callable $keySelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey|null $keySelector
     * @psalm-return EnumerableInterface<TSource>
     */
    public function distinctUntilChanged(?callable $keySelector = null): EnumerableInterface;

    /**
     * @psalm-param callable(TSource):void $action
     * @psalm-return EnumerableInterface<TSource>
     */
    public function do(callable $action): EnumerableInterface;

    /**
     * @psalm-param callable():bool $condition
     * @psalm-return EnumerableInterface<TSource>
     */
    public function doWhile(callable $condition): EnumerableInterface;

    /**
     * @psalm-return TSource
     * @throws NoSuchElementException
     */
    public function elementAt(int $index);

    /**
     * @template TDefault
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function elementAtOrDefault(int $index, $defaultValue = null);

    /**
     * @psalm-param iterable<TSource> $second
     * @psalm-param ?EqualityComparerInterface<TSource> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function except(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @psalm-param callable():void $finallyAction
     * @psalm-return EnumerableInterface<TSource>
     */
    public function finally(callable $finallyAction): EnumerableInterface;

    /**
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-return TSource
     * @throws NoSuchElementException
     */
    public function first(?callable $predicate = null);

    /**
     * @template TDefault
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function firstOrDefault(?callable $predicate = null, $defaultValue = null);

    /**
     * @psalm-param callable(TSource):void $action
     */
    public function forEach(callable $action): void;

    /**
     * @template TKey
     * @template TElement
     * @template TResult
     * @psalm-param callable(TSource):TKey $keySelector
     * @psalm-param callable(TSource):TElement|null $elementSelector
     * @psalm-param callable(TKey,TElement[]):TResult|null $resultSelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TResult>
     */
    public function groupBy(callable $keySelector, ?callable $elementSelector = null, ?callable $resultSelector = null, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

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
    public function groupJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function ignoreElements(): EnumerableInterface;

    /**
     * @psalm-param iterable<TSource> $second
     * @psalm-param ?EqualityComparerInterface<TSource> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function intersect(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    public function isEmpty(): bool;

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
    public function join(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-return TSource
     * @throws NoSuchElementException
     */
    public function last(?callable $predicate = null);

    /**
     * @template TDefault
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function lastOrDefault(?callable $predicate = null, $defaultValue = null);

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey $selector
     * @psalm-return TKey|null
     */
    public function max(?callable $selector = null);

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey $keySelector
     * @psalm-return EnumerableInterface<TSource>
     */
    public function maxBy(callable $keySelector): EnumerableInterface;

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function memoize(): EnumerableInterface;

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey|null $selector
     * @psalm-return TKey|null
     */
    public function min(?callable $selector = null);

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey $keySelector
     * @psalm-return EnumerableInterface<TSource>
     */
    public function minBy(callable $keySelector): EnumerableInterface;

    /**
     * @psalm-param iterable<TSource> ...$sources
     * @psalm-return EnumerableInterface<TSource>
     */
    public function onErrorResumeNext(iterable ...$sources): EnumerableInterface;

    /**
     * @template TInner
     * @template TKey
     * @template TResult
     * @psalm-param iterable<TInner> $inner
     * @psalm-param callable(TSource):TKey $outerKeySelector
     * @psalm-param callable(TInner):TKey $innerKeySelector
     * @psalm-param callable(TSource,TInner|null):TResult $resultSelector
     * @psalm-param ?EqualityComparerInterface<TKey> $comparer
     * @psalm-return EnumerableInterface<TResult>
     */
    public function outerJoin(iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey|null $keySelector
     * @psalm-return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderBy(?callable $keySelector = null): OrderedEnumerableInterface;

    /**
     * @template TKey
     * @psalm-param callable(TSource):TKey|null $keySelector
     * @psalm-return OrderedEnumerableInterface<TSource,TKey>
     */
    public function orderByDescending(?callable $keySelector = null): OrderedEnumerableInterface;

    /**
     * @psalm-param ?int $count
     * @psalm-return EnumerableInterface<TSource>
     */
    public function repeat(?int $count = null): EnumerableInterface;

    /**
     * @psalm-param ?int $retryCount
     * @psalm-return EnumerableInterface<TSource>
     */
    public function retry(?int $retryCount = null): EnumerableInterface;

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function reverse(): EnumerableInterface;

    /**
     * @template TAccumulate
     * @psalm-param TAccumulate $seed
     * @psalm-param callable(TAccumulate,TSource):TAccumulate $func
     * @psalm-return EnumerableInterface<TAccumulate>
     */
    public function scan($seed, callable $func);

    /**
     * @template TResult
     * @psalm-param callable(TSource,array-key):TResult $selector
     * @psalm-return EnumerableInterface<TResult>
     */
    public function select(callable $selector): EnumerableInterface;

    /**
     * @template TResult
     * @psalm-param callable(TSource,array-key):(iterable<TResult>) $collectionSelector
     * @psalm-return EnumerableInterface<TResult>
     */
    public function selectMany(callable $collectionSelector): EnumerableInterface;

    /**
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-return TSource
     * @throws NoSuchElementException
     * @throws MoreThanOneElementException
     */
    public function single(?callable $predicate = null);

    /**
     * @template TDefault
     * @psalm-param callable(TSource):bool|null $predicate
     * @psalm-param TDefault $defaultValue
     * @psalm-return TSource|TDefault
     */
    public function singleOrDefault(?callable $predicate = null, $defaultValue = null);

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function skip(int $count): EnumerableInterface;

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function skipLast(int $count): EnumerableInterface;

    /**
     * @psalm-param callable(TSource,array-key):bool $predicate
     * @psalm-return EnumerableInterface<TSource>
     */
    public function skipWhile(callable $predicate): EnumerableInterface;

    /**
     * @psalm-param TSource ...$elements
     * @psalm-return EnumerableInterface<TSource>
     */
    public function startWith(...$elements): EnumerableInterface;

    /**
     * @psalm-param callable(TSource):(int|float)|null $selector
     * @psalm-return int|float
     */
    public function sum(?callable $selector = null);

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function take(int $count): EnumerableInterface;

    /**
     * @psalm-return EnumerableInterface<TSource>
     */
    public function takeLast(int $count): EnumerableInterface;

    /**
     * @psalm-param callable(TSource,array-key):bool $predicate
     * @psalm-return EnumerableInterface<TSource>
     */
    public function takeWhile(callable $predicate): EnumerableInterface;

    /**
     * @psalm-return TSource[]
     */
    public function toArray(): array;

    /**
     * @template TElement
     * @psalm-param callable(TSource):array-key $keySelector
     * @psalm-param callable(TSource):TElement|null $elementSelector
     * @psalm-return array<array-key,TElement>
     */
    public function toDictionary(callable $keySelector, ?callable $elementSelector = null): array;

    /**
     * @template TElement
     * @psalm-param callable(TSource):array-key $keySelector
     * @psalm-param callable(TSource):TElement|null $elementSelector
     * @psalm-return array<array-key,TElement[]>
     */
    public function toLookup(callable $keySelector, ?callable $elementSelector = null): array;

    /**
     * @psalm-return \Iterator<TSource>
     */
    public function toIterator(): \Iterator;

    /**
     * @psalm-param iterable<TSource> $second
     * @psalm-param ?EqualityComparerInterface<TSource> $comparer
     * @psalm-return EnumerableInterface<TSource>
     */
    public function union(iterable $second, ?EqualityComparerInterface $comparer = null): EnumerableInterface;

    /**
     * @psalm-param callable(TSource,array-key):bool $predicate
     * @psalm-return EnumerableInterface<TSource>
     */
    public function where(callable $predicate): EnumerableInterface;

    /**
     * @psalm-param callable():bool $condition
     * @psalm-return EnumerableInterface<TSource>
     */
    public function while(callable $condition): EnumerableInterface;

    /**
     * @template TSecond
     * @template TResult
     * @psalm-param iterable<TSecond> $second
     * @psalm-param callable(TSource,TSecond):TResult $resultSelector
     * @psalm-return EnumerableInterface<TResult>
     */
    public function zip(iterable $second, callable $resultSelector): EnumerableInterface;
}
