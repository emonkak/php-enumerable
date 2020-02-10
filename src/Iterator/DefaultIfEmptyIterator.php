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
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var TSource
     */
    private $defaultValue;

    /**
     * @param iterable<TSource> $source
     * @param TSource $defaultValue
     */
    public function __construct(iterable $source, $defaultValue)
    {
        $this->source = $source;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return \Traversable<TSource>
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
