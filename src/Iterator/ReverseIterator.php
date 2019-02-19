<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Internal\Converters;

class ReverseIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @param iterable $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $array = Converters::toArray($this->source);
        for ($i = count($array) - 1; $i >= 0; $i--) {
            yield $array[$i];
        }
    }
}
