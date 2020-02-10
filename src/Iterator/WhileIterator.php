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
class WhileIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable():bool
     */
    private $condition;

    /**
     * @param iterable<TSource> $source
     * @param callable():bool $condition
     */
    public function __construct(iterable $source, callable $condition)
    {
        $this->source = $source;
        $this->condition = $condition;
    }

    /**
     * @return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        $condition = $this->condition;
        while ($condition()) {
            foreach ($this->source as $element) {
                yield $element;
            }
        }
    }
}
