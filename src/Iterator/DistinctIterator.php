<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;
use Emonkak\Enumerable\Set;

/**
 * @template TSource
 * @template TKey
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 */
class DistinctIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable(TSource):TKey
     * @var callable
     */
    private $keySelector;

    /**
     * @psalm-var EqualityComparerInterface<TKey>
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource):TKey $keySelector
     * @psalm-param EqualityComparerInterface<TKey> $comparer
     */
    public function __construct(iterable $source, callable $keySelector, EqualityComparerInterface $comparer)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->comparer = $comparer;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        $set = new Set($this->comparer);
        $keySelector = $this->keySelector;

        foreach ($this->source as $element) {
            $key = $keySelector($element);
            if (!$set->contains($key)) {
                $set->add($key);
                yield $element;
            }
        }
    }
}
