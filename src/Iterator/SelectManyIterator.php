<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class SelectManyIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var callable
     */
    private $collectionSelector;

    /**
     * @param iterable $source
     * @param callable $collectionSelector
     */
    public function __construct($source, callable $collectionSelector)
    {
        $this->source = $source;
        $this->collectionSelector = $collectionSelector;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $collectionSelector = $this->collectionSelector;
        foreach ($this->source as $element) {
            foreach ($collectionSelector($element) as $childElement) {
                yield $childElement;
            }
        }
    }
}
