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
 * @use EnumerableExtensions<TAccumulate[]>
 */
class ScanIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var TAccumulate
     * @var mixed
     */
    private $seed;

    /**
     * @psalm-var callable(TAccumulate,TSource):TAccumulate
     * @var callable
     */
    private $func;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param TAccumulate $seed
     * @psalm-param callable(TAccumulate,TSource):TAccumulate $func
     */
    public function __construct(iterable $source, $seed, callable $func)
    {
        $this->source = $source;
        $this->seed = $seed;
        $this->func = $func;
    }

    /**
     * @psalm-return \Traversable<TAccumulate[]>
     */
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
