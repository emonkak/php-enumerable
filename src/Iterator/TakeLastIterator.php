<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 */
class TakeLastIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var int
     */
    private $count;

    /**
     * @param iterable<TSource> $source
     * @param int $count
     */
    public function __construct(iterable $source, int $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

    public function getIterator(): \Traversable
    {
        $queue = new \SplQueue();
        $length = 0;

        foreach ($this->source as $element) {
            $queue->enqueue($element);
            if (++$length > $this->count) {
                $queue->dequeue();
            }
        }

        return $queue;
    }
}
