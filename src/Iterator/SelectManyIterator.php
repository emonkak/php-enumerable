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
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable(TSource):(iterable<TResult>)
     * @var callable
     */
    private $collectionSelector;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource):(iterable<TResult>) $collectionSelector
     */
    public function __construct(iterable $source, callable $collectionSelector)
    {
        $this->source = $source;
        $this->collectionSelector = $collectionSelector;
    }

    /**
     * {@inheritDoc}
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
