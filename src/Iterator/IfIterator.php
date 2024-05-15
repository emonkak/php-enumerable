<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TResult
 * @implements \IteratorAggregate<TResult>
 * @implements EnumerableInterface<TResult>
 */
class IfIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TResult>
     */
    use EnumerableExtensions;

    /**
     * @var callable():bool
     */
    private $condition;

    /**
     * @var iterable<TResult>
     */
    private iterable $thenSource;

    /**
     * @var iterable<TResult>
     */
    private iterable $elseSource;

    /**
     * @param callable():bool $condition
     * @param iterable<TResult> $thenSource
     * @param iterable<TResult> $elseSource
     */
    public function __construct(callable $condition, iterable $thenSource, iterable $elseSource)
    {
        $this->condition = $condition;
        $this->thenSource = $thenSource;
        $this->elseSource = $elseSource;
    }

    public function getIterator(): \Traversable
    {
        $condition = $this->condition;
        if ($condition()) {
            foreach ($this->thenSource as $element) {
                yield $element;
            }
        } else {
            foreach ($this->elseSource as $element) {
                yield $element;
            }
        }
    }
}
