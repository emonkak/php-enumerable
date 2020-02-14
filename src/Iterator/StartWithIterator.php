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
class StartWithIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var TSource[]
     * @var mixed[]
     */
    private $elements;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param TSource[] $elements
     */
    public function __construct(iterable $source, array $elements)
    {
        $this->source = $source;
        $this->elements = $elements;
    }

    /**
     * @psalm-return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->elements as $element) {
            yield $element;
        }
        foreach ($this->source as $element) {
            yield $element;
        }
    }
}
