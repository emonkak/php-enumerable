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
class SkipWhileIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable(TSource):bool
     * @var callable
     */
    private $predicate;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource):bool $predicate
     */
    public function __construct(iterable $source, callable $predicate)
    {
        $this->source = $source;
        $this->predicate = $predicate;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        $predicate = $this->predicate;
        $skipped = false;
        foreach ($this->source as $element) {
            if ($skipped || !$predicate($element)) {
                yield $element;
                $skipped = true;
            }
        }
    }
}
