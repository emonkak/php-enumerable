<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @template TResult
 * @implements \IteratorAggregate<TResult>
 * @implements EnumerableInterface<TResult>
 * @use EnumerableExtensions<TResult>
 */
class SelectManyIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable(TSource):(iterable<TResult>)
     */
    private $collectionSelector;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource):(iterable<TResult>) $collectionSelector
     */
    public function __construct(iterable $source, callable $collectionSelector)
    {
        $this->source = $source;
        $this->collectionSelector = $collectionSelector;
    }

    /**
     * @return \Traversable<TResult>
     */
    public function getIterator(): \Traversable
    {
        $collectionSelector = $this->collectionSelector;
        foreach ($this->source as $element) {
            foreach ($collectionSelector($element) as $childElement) {
                yield $childElement;
            }
        }
    }
}
