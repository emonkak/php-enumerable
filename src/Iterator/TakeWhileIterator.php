<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 */
class TakeWhileIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable(TSource):bool
     */
    private $predicate;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource):bool $predicate
     */
    public function __construct(iterable $source, callable $predicate)
    {
        $this->source = $source;
        $this->predicate = $predicate;
    }

    public function getIterator(): \Traversable
    {
        $predicate = $this->predicate;

        foreach ($this->source as $element) {
            if (!$predicate($element)) {
                break;
            }
            yield $element;
        }
    }
}
