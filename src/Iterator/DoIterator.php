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
class DoIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private iterable $source;

    /**
     * @var callable(TSource):void
     */
    private $action;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource):void $action
     */
    public function __construct(iterable $source, callable $action)
    {
        $this->source = $source;
        $this->action = $action;
    }

    public function getIterator(): \Traversable
    {
        $action = $this->action;
        foreach ($this->source as $element) {
            $action($element);
            yield $element;
        }
    }
}
