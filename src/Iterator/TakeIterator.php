<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 */
class TakeIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var int
     */
    private $count;

    /**
     * @param iterable<TSource> $source
     * @param int $count
     */
    public function __construct(iterable $source, int $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

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
