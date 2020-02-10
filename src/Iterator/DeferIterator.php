<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Internal\Converters;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 * @use EnumerableExtensions<TSource>
 */
class DeferIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var callable():(iterable<TSource>)
     */
    private $iterableFactory;

    /**
     * @param callable():(iterable<TSource>) $iterableFactory
     */
    public function __construct(callable $iterableFactory)
    {
        $this->iterableFactory = $iterableFactory;
    }

    /**
     * @return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        $iterableFactory = $this->iterableFactory;
        return Converters::toIterator($iterableFactory());
    }
}
