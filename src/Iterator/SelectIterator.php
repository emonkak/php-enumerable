<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @template TResult
 * @implements \IteratorAggregate<TResult>
 * @implements EnumerableInterface<TResult>
 * @use EnumerableExtensions<TResult>
 */
class SelectIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable(TSource):TResult
     */
    private $selector;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource):TResult $selector
     */
    public function __construct(iterable $source, callable $selector)
    {
        $this->source = $source;
        $this->selector = $selector;
    }

    /**
     * @return \Traversable<TResult>
     */
    public function getIterator(): \Traversable
    {
        $selector = $this->selector;
        foreach ($this->source as $element) {
            yield $selector($element);
        }
    }
}
