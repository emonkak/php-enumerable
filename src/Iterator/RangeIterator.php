<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class RangeIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var integer
     */
    private $start;

    /**
     * @var integer
     */
    private $count;

    /**
     * @param integer $start
     * @param integer $count
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
