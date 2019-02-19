<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class SelectIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var callable
     */
    private $selector;

    /**
     * @param iterable $source
     * @param callable $selector
     */
    public function __construct($source, callable $selector)
    {
        $this->source = $source;
        $this->selector = $selector;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $selector = $this->selector;
        foreach ($this->source as $element) {
            yield $selector($element);
        }
    }
}
