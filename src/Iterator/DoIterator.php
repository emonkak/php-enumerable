<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class DoIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var callable
     */
    private $action;

    /**
     * @param array|\Traversable $source
     * @param callable           $action
     */
    public function __construct($source, callable $action)
    {
        $this->source = $source;
        $this->action = $action;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $action = $this->action;
        foreach ($this->source as $element) {
            $action($element);
            yield $element;
        }
    }
}
