<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;

/**
 * @template TOuter
 * @template TInner
 * @template TKey
 * @template TResult
 */
class OuterJoinIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TOuter>
     */
    private $outer;

    /**
     * @var iterable<TInner>
     */
    private $inner;

    /**
     * @var callable(TOuter):TKey
     */
    private $outerKeySelector;

    /**
     * @var callable(TInner):TKey
     */
    private $innerKeySelector;

    /**
     * @var callable(TOuter,TInner):TResult
     */
    private $resultSelector;

    /**
     * @var EqualityComparerInterface<TKey>
     */
    private $comparer;

    /**
     * @param iterable<TOuter> $outer
     * @param iterable<TInner> $inner
     * @param callable(TOuter):TKey $outerKeySelector
     * @param callable(TInner):TKey $innerKeySelector
     * @param callable(TOuter,TInner):TResult $resultSelector
     * @param EqualityComparerInterface<TKey> $comparer
     */
    public function __construct(iterable $outer, iterable $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, EqualityComparerInterface $comparer)
    {
        $this->outer = $outer;
        $this->inner = $inner;
        $this->outerKeySelector = $outerKeySelector;
        $this->innerKeySelector = $innerKeySelector;
        $this->resultSelector = $resultSelector;
        $this->comparer = $comparer;
    }

    public function getIterator(): \Traversable
    {
        $outerKeySelector = $this->outerKeySelector;
        $innerKeySelector = $this->innerKeySelector;
        $resultSelector = $this->resultSelector;

        $lookup = [];

        foreach ($this->inner as $innerElement) {
            $key = $innerKeySelector($innerElement);
            $hash = $this->comparer->hash($key);
            $lookup[$hash][] = $innerElement;
        }

        foreach ($this->outer as $outerElement) {
            $key = $outerKeySelector($outerElement);
            $hash = $this->comparer->hash($key);

            if (isset($lookup[$hash])) {
                foreach ($lookup[$hash] as $innerElement) {
                    yield $resultSelector($outerElement, $innerElement);
                }
            } else {
                yield $resultSelector($outerElement, null);
            }
        }
    }
}
