<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 */
class StartWithIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var TSource[]
     */
    private $elements;

    /**
     * @param iterable<TSource> $source
     * @param TSource[] $elements
     */
    public function __construct(iterable $source, array $elements)
    {
        $this->source = $source;
        $this->elements = $elements;
    }

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
