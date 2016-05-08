<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class WhereIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    private $source;

    private $predicate;

    public function __construct($source, callable $predicate)
    {
        $this->source = $source;
        $this->predicate = $predicate;
    }

    public function getIterator()
    {
        $predicate = $this->predicate;

        foreach ($this->source as $element) {
            if ($predicate($element)) {
                yield $element;
            }
        }
    }
}
