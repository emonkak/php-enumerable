<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class ConcatIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    private $first;

    private $second;

    public function __construct($first, $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function getIterator()
    {
        foreach ($this->first as $element) {
            yield $element;
        }
        foreach ($this->second as $element) {
            yield $element;
        }
    }
}
