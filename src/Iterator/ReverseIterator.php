<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Internal\Converters;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 */
class ReverseIterator implements \IteratorAggregate, EnumerableInterface
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
     * @psalm-param iterable<TSource> $source
     */
    public function __construct(iterable $source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        $array = Converters::toArray($this->source);
        for ($i = count($array) - 1; $i >= 0; $i--) {
            yield $array[$i];
        }
    }
}
