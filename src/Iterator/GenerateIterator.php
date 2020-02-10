<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TState
 * @template TResult
 */
class GenerateIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var TState
     */
    private $initialState;

    /**
     * @var callable(TState):bool
     */
    private $condition;

    /**
     * @var callable(TState):TState
     */
    private $iterate;

    /**
     * @var callable(TState):TResult
     */
    private $resultSelector;

    /**
     * @param TState $initialState
     * @param callable(TState):bool $condition
     * @param callable(TState):TState $iterate
     * @param callable(TState):TResult $resultSelector
     */
    public function __construct($initialState, callable $condition, callable $iterate, callable $resultSelector)
    {
        $this->initialState = $initialState;
        $this->condition = $condition;
        $this->iterate = $iterate;
        $this->resultSelector = $resultSelector;
    }

    public function getIterator(): \Traversable
    {
        $condition = $this->condition;
        $iterate = $this->iterate;
        $resultSelector = $this->resultSelector;
        for ($state = $this->initialState; $condition($state); $state = $iterate($state)) {
            yield $resultSelector($state);
        }
    }
}
