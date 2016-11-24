<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class DeferIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var callable
     */
    private $traversableFactory;

    /**
     * @param callable $traversableFactory
     */
    public function __construct(callable $traversableFactory)
    {
        $this->traversableFactory = $traversableFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $traversableFactory = $this->traversableFactory;
        return $traversableFactory();
    }
}
