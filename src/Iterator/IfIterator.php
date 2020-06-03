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
     * @psalm-var callable():bool
     * @var callable
     */
    private $condition;

    /**
     * @psalm-var iterable<TResult>
     * @var iterable
     */
    private $thenSource;

    /**
     * @psalm-var iterable<TResult>
     * @var iterable
     */
    private $elseSource;

    /**
     * @psalm-param callable():bool $condition
     * @psalm-param iterable<TResult> $thenSource
     * @psalm-param iterable<TResult> $elseSource
     */
    public function __construct(callable $condition, iterable $thenSource, iterable $elseSource)
    {
        $this->condition = $condition;
        $this->thenSource = $thenSource;
        $this->elseSource = $elseSource;
    }

    /**
     * {@inheritdoc}
     */
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
