<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;

/**
 * @template TSource
 * @template TKey
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 * @use EnumerableExtensions<TSource>
 */
class DistinctUntilChangedIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable(TSource):TKey
     */
    private $keySelector;

    /**
     * @var EqualityComparerInterface<TKey>
     */
    private $comparer;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource):TKey $keySelector
     * @param EqualityComparerInterface<TKey> $comparer
     */
    public function __construct(iterable $source, callable $keySelector, EqualityComparerInterface $comparer)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->comparer = $comparer;
    }

    /**
     * @return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        $hasCurrentHash = false;
        $currentHash = null;
        $keySelector = $this->keySelector;
        $comparer = $this->comparer;

        foreach ($this->source as $element) {
            $key = $keySelector($element);
            $hash = $comparer->hash($key);
            if (!$hasCurrentHash || $currentHash !== $hash) {
                $hasCurrentHash = true;
                $currentHash = $hash;
                yield $element;
            }
        }
    }
}
