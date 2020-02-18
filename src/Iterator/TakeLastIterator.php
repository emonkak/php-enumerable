<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 */
class TakeLastIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @var int
     */
    private $count;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param int $count
     */
    public function __construct(iterable $source, int $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
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
