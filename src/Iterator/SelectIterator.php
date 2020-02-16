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
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable(TSource):TResult
     * @var callable
     */
    private $selector;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource):TResult $selector
     */
    public function __construct(iterable $source, callable $selector)
    {
        $this->source = $source;
        $this->selector = $selector;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        $selector = $this->selector;
        foreach ($this->source as $element) {
            yield $selector($element);
        }
    }
}
