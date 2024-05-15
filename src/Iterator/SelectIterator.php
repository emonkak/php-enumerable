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
 */
class SelectIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TResult>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private iterable $source;

    /**
     * @var callable(TSource,array-key):TResult
     */
    private $selector;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource,array-key):TResult $selector
     */
    public function __construct(iterable $source, callable $selector)
    {
        $this->source = $source;
        $this->selector = $selector;
    }

    public function getIterator(): \Traversable
    {
        $selector = $this->selector;
        foreach ($this->source as $key => $element) {
            yield $selector($element, $key);
        }
    }
}
