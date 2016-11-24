<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class TakeIterator implements \IteratorAggregate, EnumerableInterface
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
