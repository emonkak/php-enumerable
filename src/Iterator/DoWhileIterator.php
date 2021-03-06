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
class DoWhileIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable():bool
     * @var callable
     */
    private $condition;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable():bool $condition
     */
    public function __construct(iterable $source, callable $condition)
    {
        $this->source = $source;
        $this->condition = $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        $condition = $this->condition;
        do {
            foreach ($this->source as $element) {
                yield $element;
            }
        } while ($condition());
    }
}
