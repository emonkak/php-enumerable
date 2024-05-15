<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @template TAccumulate
 * @implements \IteratorAggregate<TAccumulate[]>
 * @implements EnumerableInterface<TAccumulate[]>
 */
class ScanIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TAccumulate[]>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private iterable $source;

    /**
     * @var TAccumulate
     */
    private mixed $seed;

    /**
     * @var callable(TAccumulate,TSource):TAccumulate
     */
    private $func;

    /**
     * @param iterable<TSource> $source
     * @param TAccumulate $seed
     * @param callable(TAccumulate,TSource):TAccumulate $func
     */
    public function __construct(iterable $source, $seed, callable $func)
    {
        $this->source = $source;
        $this->seed = $seed;
        $this->func = $func;
    }

    public function getIterator(): \Traversable
    {
        $result = $this->seed;
        $func = $this->func;
        foreach ($this->source as $element) {
            $result = $func($result, $element);
            yield $result;
        }
    }
}
