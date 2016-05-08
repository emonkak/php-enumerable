<?php

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Iterator\EmptyIterator;

class Enumerable implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    private $source;

    public static function _empty()
    {
        return new EmptyIterator();
    }

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function getIterator()
    {
        foreach ($this->source as $element) {
            yield $element;
        }
    }
}
