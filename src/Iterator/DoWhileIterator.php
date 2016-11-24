<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class DoWhileIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var callable
     */
    private $condition;

    /**
     * @param array|\Traversable $source
     * @param callable           $condition
     */
    public function __construct($source, callable $condition)
    {
        $this->source = $source;
        $this->condition = $condition;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $condition = $this->condition;
        do {
            foreach ($this->source as $element) {
                yield $element;
            }
        } while ($condition());
    }
}
