<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @implements \IteratorAggregate<int>
 * @implements EnumerableInterface<int>
 * @use EnumerableExtensions<int>
 */
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
     * @psalm-param int $start
     * @psalm-param int $count
     */
    public function __construct(int $start, int $count)
    {
        $this->start = $start;
        $this->count = $count;
    }

    /**
     * @psalm-return \Traversable<int>
     */
    public function getIterator(): \Traversable
    {
        $current = $this->start;
        $end = $this->start + $this->count;
        do {
            yield $current++;
        } while ($current < $end);
    }
}
