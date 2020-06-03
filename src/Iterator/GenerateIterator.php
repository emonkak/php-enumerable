<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TState
 * @template TResult
 * @implements \IteratorAggregate<TResult>
 * @implements EnumerableInterface<TResult>
 */
class GenerateIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TResult>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var TState
     * @var mixed
     */
    private $initialState;

    /**
     * @psalm-var callable(TState):bool
     * @var callable
     */
    private $condition;

    /**
     * @psalm-var callable(TState):TState
     * @var callable
     */
    private $iterate;

    /**
     * @psalm-var callable(TState):TResult
     * @var callable
     */
    private $resultSelector;

    /**
     * @psalm-param TState $initialState
     * @psalm-param callable(TState):bool $condition
     * @psalm-param callable(TState):TState $iterate
     * @psalm-param callable(TState):TResult $resultSelector
     */
    public function __construct($initialState, callable $condition, callable $iterate, callable $resultSelector)
    {
        $this->initialState = $initialState;
        $this->condition = $condition;
        $this->iterate = $iterate;
        $this->resultSelector = $resultSelector;
    }

    /**
     * {@inheritdoc}
     */
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
