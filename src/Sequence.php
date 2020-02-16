<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T
 * @implements \IteratorAggregate<T>
 * @implements EnumerableInterface<T>
 * @use EnumerableExtensions<T>
 */
class Sequence implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<T>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-param iterable<T> $source
     */
    public function __construct(iterable $source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->source as $element) {
            yield $element;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSource(): iterable
    {
        return $this->source;
    }
}
