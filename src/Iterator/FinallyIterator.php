<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 * @use EnumerableExtensions<TSource>
 */
class FinallyIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable():void
     */
    private $finallyAction;

    /**
     * @param iterable<TSource> $source
     * @param callable():void $finallyAction
     */
    public function __construct(iterable $source, callable $finallyAction)
    {
        $this->source = $source;
        $this->finallyAction = $finallyAction;
    }

    /**
     * @return \Traversable<TSource>
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
