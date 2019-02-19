<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;
use Emonkak\Enumerable\Set;

class UnionIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $first;

    /**
     * @var iterable
     */
    private $second;

    /**
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @param iterable $first
     * @param iterable $second
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
        $set->addAll($this->first);
        foreach ($this->second as $element) {
            if ($set->contains($element)) {
                yield $element;
            }
        }
    }
}
