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
class ConcatIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>[]
     */
    private array $sources;

    /**
     * @param iterable<TSource>[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->sources as $source) {
            foreach ($source as $element) {
                yield $element;
            }
        }
    }
}
