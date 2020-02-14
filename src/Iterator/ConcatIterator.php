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
class ConcatIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>[]
     * @var iterable
     */
    private $sources;

    /**
     * @psalm-param iterable<TSource>[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    /**
     * @psalm-return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->sources as $source) {
            foreach ($source as $element) {
                yield $element;
            }
        }
    }
}
