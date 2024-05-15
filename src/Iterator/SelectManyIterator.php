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
 */
class SelectManyIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TResult>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable(TSource,array-key):(iterable<TResult>)
     */
    private $collectionSelector;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource,array-key):iterable<TResult> $collectionSelector
     */
    public function __construct(iterable $source, callable $collectionSelector)
    {
        $this->source = $source;
        $this->collectionSelector = $collectionSelector;
    }

    public function getIterator(): \Traversable
    {
        $collectionSelector = $this->collectionSelector;
        foreach ($this->source as $key => $element) {
            foreach ($collectionSelector($element, $key) as $childElement) {
                yield $childElement;
            }
        }
    }
}
