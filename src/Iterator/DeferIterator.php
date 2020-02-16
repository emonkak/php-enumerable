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
     * @psalm-var callable():(iterable<TSource>)
     * @var callable
     */
    private $iterableFactory;

    /**
     * @psalm-param callable():(iterable<TSource>) $iterableFactory
     */
    public function __construct(callable $iterableFactory)
    {
        $this->iterableFactory = $iterableFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        $iterableFactory = $this->iterableFactory;
        return Converters::toIterator($iterableFactory());
    }
}
