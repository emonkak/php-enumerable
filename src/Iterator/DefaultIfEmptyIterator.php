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
class DefaultIfEmptyIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var TSource
     * @var mixed
     */
    private $defaultValue;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param TSource $defaultValue
     */
    public function __construct(iterable $source, $defaultValue)
    {
        $this->source = $source;
        $this->defaultValue = $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
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
