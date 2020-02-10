<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 */
class DeferIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var callable():(iterable<TSource>)
     */
    private $traversableFactory;

    /**
     * @param callable():(iterable<TSource>) $traversableFactory
     */
    public function __construct(callable $traversableFactory)
    {
        $this->traversableFactory = $traversableFactory;
    }

    public function getIterator(): \Traversable
    {
        $traversableFactory = $this->traversableFactory;
        return $traversableFactory();
    }
}
