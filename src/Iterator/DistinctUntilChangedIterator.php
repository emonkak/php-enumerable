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
     * @psalm-return \Traversable<TSource>
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
