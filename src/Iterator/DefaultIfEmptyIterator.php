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
class DefaultIfEmptyIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private iterable $source;

    /**
     * @var TSource
     */
    private mixed $defaultValue;

    /**
     * @param iterable<TSource> $source
     * @param TSource $defaultValue
     */
    public function __construct(iterable $source, mixed $defaultValue)
    {
        $this->source = $source;
        $this->defaultValue = $defaultValue;
    }

    public function getIterator(): \Traversable
    {
        $hasValue = false;

        foreach ($this->source as $element) {
            yield $element;
            $hasValue = true;
        }

        if (!$hasValue) {
            yield $this->defaultValue;
        }
    }
}
