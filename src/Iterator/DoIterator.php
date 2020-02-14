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
class DoIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable(TSource):void
     * @var callable
     */
    private $action;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource):void $action
     */
    public function __construct(iterable $source, callable $action)
    {
        $this->source = $source;
        $this->action = $action;
    }

    /**
     * @psalm-return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        $action = $this->action;
        foreach ($this->source as $element) {
            $action($element);
            yield $element;
        }
    }
}
