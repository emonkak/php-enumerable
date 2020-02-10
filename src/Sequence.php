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
     * @var iterable<T>
     */
    private $source;

    /**
     * @param iterable<T> $source
     */
    public function __construct(iterable $source)
    {
        $this->source = $source;
    }

    /**
     * @return \Traversable<T>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->source as $element) {
            yield $element;
        }
    }

    /**
     * @return iterable<T>
     */
    public function getSource(): iterable
    {
        return $this->source;
    }
}
