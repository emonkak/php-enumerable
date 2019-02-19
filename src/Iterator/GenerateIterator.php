<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class GenerateIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var mixed
     */
    private $initialState;

    /**
     * @var callable
     */
    private $condition;

    /**
     * @var callable
     */
    private $iterate;

    /**
     * @var callable
     */
    private $resultSelector;

    /**
     * @param mixed $initialState
     * @param callable $condition
     * @param callable $iterate
     * @param callable $resultSelector
     */
    public function __construct($initialState, callable $condition, callable $iterate, callable $resultSelector)
    {
        $this->initialState = $initialState;
        $this->condition = $condition;
        $this->iterate = $iterate;
        $this->resultSelector = $resultSelector;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $condition = $this->condition;
        $iterate = $this->iterate;
        $resultSelector = $this->resultSelector;
        for ($state = $this->initialState; $condition($state); $state = $iterate($state)) {
            yield $resultSelector($state);
        }
    }
}
