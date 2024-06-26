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
class RepeatIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private iterable $source;

    private ?int $count;

    /**
     * @param iterable<TSource> $source
     */
    public function __construct(iterable $source, ?int $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

    public function getIterator(): \Traversable
    {
        if ($this->count === null) {
            while (true) {
                foreach ($this->source as $element) {
                    yield $element;
                }
            }
        } else {
            for ($i = $this->count; $i > 0; $i--) {
                foreach ($this->source as $element) {
                    yield $element;
                }
            }
        }
    }
}
