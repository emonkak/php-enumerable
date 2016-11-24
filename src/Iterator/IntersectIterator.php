<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Set;

class IntersectIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $first;

    /**
     * @var array|\Traversable
     */
    private $second;

    /**
     * @param array|\Traversable $first
     * @param array|\Traversable $second
     */
    public function __construct($first, $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $set = Set::from($this->second);
        foreach ($this->first as $element) {
            if ($set->contains($element)) {
                yield $element;
            }
        }
    }
}
