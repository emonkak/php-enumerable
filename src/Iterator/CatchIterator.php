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
class CatchIterator implements \IteratorAggregate, EnumerableInterface
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
     * @var callable(\Exception):(iterable<TSource>)
     */
    private $handler;

    /**
     * @param iterable<TSource> $source
     * @param callable(\Exception):(iterable<TSource>) $handler
     */
    public function __construct(iterable $source, callable $handler)
    {
        $this->source = $source;
        $this->handler = $handler;
    }

    public function getIterator(): \Traversable
    {
        try {
            foreach ($this->source as $element) {
                yield $element;
            }
        } catch (\Exception $e) {
            $handler = $this->handler;
            foreach ($handler($e) as $element) {
                yield $element;
            }
        }
    }
}
