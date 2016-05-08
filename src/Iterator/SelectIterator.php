<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class SelectIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    private $source;

    private $selector;

    public function __construct($source, callable $selector)
    {
        $this->source = $source;
        $this->selector = $selector;
    }

    public function getIterator()
    {
        $selector = $this->selector;

        foreach ($this->source as $element) {
            yield $selector($element);
        }
    }
}
