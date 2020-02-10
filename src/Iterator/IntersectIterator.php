<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;
use Emonkak\Enumerable\Set;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 * @use EnumerableExtensions<TSource>
 */
class IntersectIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $first;

    /**
     * @var iterable<TSource>
     */
    private $second;

    /**
     * @var EqualityComparerInterface<TSource>
     */
    private $comparer;

    /**
     * @param iterable<TSource> $first
     * @param iterable<TSource> $second
     * @param EqualityComparerInterface<TSource> $comparer
     */
    public function __construct(iterable $first, iterable $second, EqualityComparerInterface $comparer)
    {
        $this->first = $first;
        $this->second = $second;
        $this->comparer = $comparer;
    }

    /**
     * @return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        $set = new Set($this->comparer);
        foreach ($this->second as $element) {
            $set->add($element);
        }
        foreach ($this->first as $element) {
            if ($set->contains($element)) {
                yield $element;
            }
        }
    }
}
