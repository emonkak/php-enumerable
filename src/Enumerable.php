<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Internal\Errors;
use Emonkak\Enumerable\Iterator\ConcatIterator;
use Emonkak\Enumerable\Iterator\DeferIterator;
use Emonkak\Enumerable\Iterator\EmptyIterator;
use Emonkak\Enumerable\Iterator\GenerateIterator;
use Emonkak\Enumerable\Iterator\IfIterator;
use Emonkak\Enumerable\Iterator\OnErrorResumeNextIterator;
use Emonkak\Enumerable\Iterator\RangeIterator;
use Emonkak\Enumerable\Iterator\StaticCatchIterator;
use Emonkak\Enumerable\Iterator\StaticRepeatIterator;
use Emonkak\Enumerable\Iterator\ZipIterator;

final class Enumerable
{
    /**
     * @template TSource extends object
     * @param iterable<TSource> $source
     * @return EnumerableInterface<TSource>
     */
    public static function from(iterable $source): EnumerableInterface
    {
        return new Sequence($source);
    }

    /**
     * @template TSource
     * @param iterable<TSource>[] ...$sources
     * @return EnumerableInterface<TSource>
     */
    public static function catch(iterable ...$sources): EnumerableInterface
    {
        return new StaticCatchIterator($sources);
    }

    /**
     * @template TSource
     * @param iterable<TSource>[] ...$sources
     * @return EnumerableInterface<TSource>
     */
    public static function concat(iterable ...$sources): EnumerableInterface
    {
        return new ConcatIterator($sources);
    }

    /**
     * @template TSource
     * @param callable():(iterable<TSource>) $iterableFactory
     * @return EnumerableInterface<TSource>
     */
    public static function defer(callable $iterableFactory): EnumerableInterface
    {
        return new DeferIterator($iterableFactory);
    }

    /**
     * @template TState
     * @template TResult
     * @param TState $initialState
     * @param callable(TState):bool $condition
     * @param callable(TState):TState $iterate
     * @param callable(TState):TResult $resultSelector
     * @return EnumerableInterface<TResult>
     */
    public static function generate($initialState, callable $condition, callable $iterate, callable $resultSelector): EnumerableInterface
    {
        return new GenerateIterator($initialState, $condition, $iterate, $resultSelector);
    }

    /**
     * @template TResult
     * @param callable():bool $condition
     * @param iterable<TResult> $thenSource
     * @param iterable<TResult> $elseSource
     * @return EnumerableInterface<TResult>
     */
    public static function if(callable $condition, iterable $thenSource, iterable $elseSource): EnumerableInterface
    {
        return new IfIterator($condition, $thenSource, $elseSource);
    }

    /**
     * @template TSource
     * @param iterable<TSource>[] ...$sources
     * @return EnumerableInterface<TSource>
     */
    public static function onErrorResumeNext(iterable ...$sources): EnumerableInterface
    {
        return new OnErrorResumeNextIterator($sources);
    }

    /**
     * @return EnumerableInterface<int>
     */
    public static function range(int $start, int $count): EnumerableInterface
    {
        return new RangeIterator($start, $count);
    }

    /**
     * @template TSource
     * @param TSource $element
     * @return EnumerableInterface<TSource>
     */
    public static function repeat($element, ?int $count = null): EnumerableInterface
    {
        if ($count < 0) {
            throw Errors::argumentOutOfRange('count');
        }
        return new StaticRepeatIterator($element, $count);
    }

    /**
     * @template TSource
     * @param TSource $element
     * @return EnumerableInterface<TSource>
     */
    public static function return($element): EnumerableInterface
    {
        return new Sequence([$element]);
    }

    /**
     * @template TFirst
     * @template TSecond
     * @template TResult
     * @param iterable<TFirst> $first
     * @param iterable<TSecond> $second
     * @param callable(TFirst,TSecond):TResult $resultSelector
     * @return EnumerableInterface<TResult>
     */
    public static function zip(iterable $first, iterable $second, callable $resultSelector): EnumerableInterface
    {
        return new ZipIterator($first, $second, $resultSelector);
    }

    /**
     * @return EnumerableInterface<mixed>
     */
    public static function empty(): EnumerableInterface
    {
        return new EmptyIterator();
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
