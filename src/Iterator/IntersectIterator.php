<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\HasherInterface;
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
     * @var HasherInterface
     */
    private $hasher;

    /**
     * @param array|\Traversable $first
     * @param array|\Traversable $second
     * @param HasherInterface    $hasher
     */
    public function __construct($first, $second, HasherInterface $hasher)
    {
        $this->first = $first;
        $this->second = $second;
        $this->hasher = $hasher;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $set = new Set($this->hasher);
        $set->addAll($this->second);
        foreach ($this->first as $element) {
            if ($set->contains($element)) {
                yield $element;
            }
        }
    }
}
