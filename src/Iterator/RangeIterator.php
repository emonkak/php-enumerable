<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class RangeIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $count;

    /**
     * @param int $start
     * @param int $count
     */
    public function __construct($start, $count)
    {
        $this->start = $start;
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $current = $this->start;
        $end = $this->start + $this->count;
        do {
            yield $current++;
        } while ($current < $end);
    }
}
