<?php

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
     * @param array|\Traversable $source
     * @return EnumerableInterface
     */
    public static function from($source)
    {
        if (!(is_array($source) || $source instanceof \Traversable)) {
            $type = gettype($source);
            throw new \RuntimeException("The source must be an array or traversable object, got '$type'");
        }
        return new Sequence($source);
    }

    /**
     * @param array[]|\Traversable[] ...$sources
     * @return EnumerableInterface
     */
    public static function _catch(...$sources)
    {
        return new StaticCatchIterator($sources);
    }

    /**
     * @param array[]|\Traversable[] ...$sources
     * @return EnumerableInterface
     */
    public static function concat(...$sources)
    {
        return new ConcatIterator($sources);
    }

    /**
     * @param callable $traversableFactory
     * @return EnumerableInterface
     */
    public static function defer(callable $traversableFactory)
    {
        return new DeferIterator($traversableFactory);
    }

    /**
     * @param mixed    $initialState
     * @param callable $condition
     * @param callable $iterate
     * @param callable $resultSelector
     * @return EnumerableInterface
     */
    public static function generate($initialState, callable $condition, callable $iterate, callable $resultSelector)
    {
        return new GenerateIterator($initialState, $condition, $iterate, $resultSelector);
    }

    /**
     * @param callable           $condition
     * @param array|\Traversable $thenSource
     * @param array|\Traversable $elseSource
     * @return EnumerableInterface
     */
    public static function _if(callable $condition, $thenSource, $elseSource)
    {
        return new IfIterator($condition, $thenSource, $elseSource);
    }

    /**
     * @param array[]|\Traversable[] ...$sources
     * @return EnumerableInterface
     */
    public static function onErrorResumeNext(...$sources)
    {
        return new OnErrorResumeNextIterator($sources);
    }

    /**
     * @param integer $start
     * @param integer $count
     * @return EnumerableInterface
     */
    public static function range($start, $count)
    {
        return new RangeIterator($start, $count);
    }

    /**
     * @param mixed   $element
     * @param integer $count
     * @return EnumerableInterface
     */
    public static function repeat($element, $count = null)
    {
        if ($count < 0) {
            throw Errors::argumentOutOfRange('count');
        }
        return new StaticRepeatIterator($element, $count);
    }

    /**
     * @param mixed $element
     * @return EnumerableInterface
     */
    public static function _return($element)
    {
        return new Sequence([$element]);
    }

    /**
     * @param array|\Traversable $first
     * @param array|\Traversable $second
     * @return EnumerableInterface
     */
    public static function zip($first, $second, callable $resultSelector)
    {
        return new ZipIterator($first, $second, $resultSelector);
    }

    /**
     * @return EnumerableInterface
     */
    public static function _empty()
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
