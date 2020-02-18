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
     * @psalm-param iterable<TSource> $source
     * @psalm-return EnumerableInterface<TSource>
     */
    public static function from(iterable $source): EnumerableInterface
    {
        return new Sequence($source);
    }

    /**
     * @template TSource
     * @psalm-param iterable<TSource> ...$sources
     * @psalm-return EnumerableInterface<TSource>
     */
    public static function catch(iterable ...$sources): EnumerableInterface
    {
        return new StaticCatchIterator($sources);
    }

    /**
     * @template TSource
     * @psalm-param iterable<TSource> ...$sources
     * @psalm-return EnumerableInterface<TSource>
     */
    public static function concat(iterable ...$sources): EnumerableInterface
    {
        return new ConcatIterator($sources);
    }

    /**
     * @template TSource
     * @psalm-param callable():(iterable<TSource>) $iterableFactory
     * @psalm-return EnumerableInterface<TSource>
     */
    public static function defer(callable $iterableFactory): EnumerableInterface
    {
        return new DeferIterator($iterableFactory);
    }

    /**
     * @template TState
     * @template TResult
     * @psalm-param TState $initialState
     * @psalm-param callable(TState):bool $condition
     * @psalm-param callable(TState):TState $iterate
     * @psalm-param callable(TState):TResult $resultSelector
     * @psalm-return EnumerableInterface<TResult>
     */
    public static function generate($initialState, callable $condition, callable $iterate, callable $resultSelector): EnumerableInterface
    {
        return new GenerateIterator($initialState, $condition, $iterate, $resultSelector);
    }

    /**
     * @template TResult
     * @psalm-param callable():bool $condition
     * @psalm-param iterable<TResult> $thenSource
     * @psalm-param iterable<TResult> $elseSource
     * @psalm-return EnumerableInterface<TResult>
     */
    public static function if(callable $condition, iterable $thenSource, iterable $elseSource): EnumerableInterface
    {
        return new IfIterator($condition, $thenSource, $elseSource);
    }

    /**
     * @template TSource
     * @psalm-param iterable<TSource> ...$sources
     * @psalm-return EnumerableInterface<TSource>
     */
    public static function onErrorResumeNext(iterable ...$sources): EnumerableInterface
    {
        return new OnErrorResumeNextIterator($sources);
    }

    /**
     * @psalm-return EnumerableInterface<int>
     */
    public static function range(int $start, int $count): EnumerableInterface
    {
        return new RangeIterator($start, $count);
    }

    /**
     * @template TSource
     * @psalm-param TSource $element
     * @psalm-return EnumerableInterface<TSource>
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
     * @psalm-param TSource $element
     * @psalm-return EnumerableInterface<TSource>
     */
    public static function return($element): EnumerableInterface
    {
        return new Sequence([$element]);
    }

    /**
     * @template TFirst
     * @template TSecond
     * @template TResult
     * @psalm-param iterable<TFirst> $first
     * @psalm-param iterable<TSecond> $second
     * @psalm-param callable(TFirst,TSecond):TResult $resultSelector
     * @psalm-return EnumerableInterface<TResult>
     */
    public static function zip(iterable $first, iterable $second, callable $resultSelector): EnumerableInterface
    {
        return new ZipIterator($first, $second, $resultSelector);
    }

    /**
     * @template TSource
     * @psalm-return EnumerableInterface<TSource>
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
