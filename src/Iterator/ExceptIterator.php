<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;
use Emonkak\Enumerable\Set;

class ExceptIterator implements \IteratorAggregate, EnumerableInterface
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
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @param array|\Traversable        $first
     * @param array|\Traversable        $second
     * @param EqualityComparerInterface $comparer
     */
    public function __construct($first, $second, EqualityComparerInterface $comparer)
    {
        $this->first = $first;
        $this->second = $second;
        $this->comparer = $comparer;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $set = new Set($this->comparer);
        $set->addAll($this->second);
        foreach ($this->first as $element) {
            if (!$set->contains($element)) {
                $set->add($element);
                yield $element;
            }
        }
    }
}
