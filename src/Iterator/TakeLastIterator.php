<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class TakeLastIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var integer
     */
    private $count;

    /**
     * @param array|\Traversable $source
     * @param integer            $count
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
