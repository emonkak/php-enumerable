<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class TakeIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var int
     */
    private $count;

    /**
     * @param iterable $source
     * @param int $count
     */
    public function __construct($source, $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $count = $this->count;
        if ($count <= 0) {
            return;
        }
        foreach ($this->source as $element) {
            yield $element;
            if (--$count <= 0) {
                break;
            }
        }
    }
}
