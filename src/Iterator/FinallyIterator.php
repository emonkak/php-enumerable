<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 */
class FinallyIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable():void
     * @var callable
     */
    private $finallyAction;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable():void $finallyAction
     */
    public function __construct(iterable $source, callable $finallyAction)
    {
        $this->source = $source;
        $this->finallyAction = $finallyAction;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
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
