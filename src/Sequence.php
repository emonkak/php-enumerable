<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template TSource
 */
class Sequence implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @param iterable<TSource> $source
     */
    public function __construct(iterable $source)
    {
        $this->source = $source;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->source as $element) {
            yield $element;
        }
    }

    /**
     * @retrurn iterable<TSource>
     */
    public function getSource(): iterable
    {
        return $this->source;
    }
}
