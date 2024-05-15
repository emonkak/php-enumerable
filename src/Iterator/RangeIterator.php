<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @implements \IteratorAggregate<int>
 * @implements EnumerableInterface<int>
 */
class RangeIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<int>
     */
    use EnumerableExtensions;

    private int $start;

    private int $count;

    public function __construct(int $start, int $count)
    {
        $this->start = $start;
        $this->count = $count;
    }

    public function getIterator(): \Traversable
    {
        $current = $this->start;
        $end = $this->start + $this->count;
        do {
            yield $current++;
        } while ($current < $end);
    }
}
