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
class TakeIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @var int
     */
    private $count;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param int $count
     */
    public function __construct(iterable $source, int $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

    /**
     * @psalm-return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
    {
        $count = $this->count;
        if ($count <= 0) {
            return;
        }
        foreach ($this->source as $element) {
            yield $element;
            if (--$count <= 0) {
                break;
            }
        }
    }
}
