<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Internal\Converters;

/**
 * @template TFirst
 * @template TSecond
 * @template TResult
 * @implements \IteratorAggregate<TResult>
 * @implements EnumerableInterface<TResult>
 */
class ZipIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TResult>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TFirst>
     */
    private iterable $first;

    /**
     * @var iterable<TSecond>
     */
    private iterable $second;

    /**
     * @var callable(TFirst,TSecond):TResult
     */
    private $resultSelector;

    /**
     * @param iterable<TFirst> $first
     * @param iterable<TSecond> $second
     * @param callable(TFirst,TSecond):TResult $resultSelector
     */
    public function __construct(iterable $first, iterable $second, callable $resultSelector)
    {
        $this->first = $first;
        $this->second = $second;
        $this->resultSelector = $resultSelector;
    }

    public function getIterator(): \Traversable
    {
        $resultSelector = $this->resultSelector;
        $first = Converters::toIterator($this->first);
        $second = Converters::toIterator($this->second);

        $first->rewind();
        $second->rewind();

        while ($first->valid() && $second->valid()) {
            yield $resultSelector($first->current(), $second->current());
            $first->next();
            $second->next();
        }
    }
}
