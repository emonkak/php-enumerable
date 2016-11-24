<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class FinallyIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var callable
     */
    private $finallyAction;

    /**
     * @param array|\Traversable $source
     * @param callable           $finallyAction
     */
    public function __construct($source, callable $finallyAction)
    {
        $this->source = $source;
        $this->finallyAction = $finallyAction;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        try {
            foreach ($this->source as $element) {
                yield $element;
            }
        } finally {
            $finallyAction = $this->finallyAction;
            $finallyAction();
        }
    }
}
