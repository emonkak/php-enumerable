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
 * @implements \IteratorAggregate<TResult>
 * @implements EnumerableInterface<TResult>
 */
class JoinIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TResult>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TOuter>
     * @var iterable
     */
    private $outer;

    /**
     * @psalm-var iterable<TInner>
     * @var iterable
     */
    private $inner;

    /**
     * @psalm-var callable(TOuter):TKey
     * @var callable
     */
    private $outerKeySelector;

    /**
     * @psalm-var callable(TInner):TKey
     * @var callable
     */
    private $innerKeySelector;

    /**
     * @psalm-var callable(TOuter,TInner):TResult
     * @var callable
     */
    private $resultSelector;

    /**
     * @psalm-var EqualityComparerInterface<TKey>
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @psalm-param iterable<TOuter> $outer
     * @psalm-param iterable<TInner> $inner
     * @psalm-param callable(TOuter):TKey $outerKeySelector
     * @psalm-param callable(TInner):TKey $innerKeySelector
     * @psalm-param callable(TOuter,TInner):TResult $resultSelector
     * @psalm-param EqualityComparerInterface<TKey> $comparer
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

    /**
     * {@inheritDoc}
     */
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
            }
        }
    }
}
