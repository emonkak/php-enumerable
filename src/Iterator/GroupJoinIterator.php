<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;

class GroupJoinIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $outer;

    /**
     * @var iterable
     */
    private $inner;

    /**
     * @var callable
     */
    private $outerKeySelector;

    /**
     * @var callable
     */
    private $innerKeySelector;

    /**
     * @var callable
     */
    private $resultSelector;

    /**
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @param iterable $outer
     * @param iterable $inner
     * @param callable $outerKeySelector
     * @param callable $innerKeySelector
     * @param callable $resultSelector
     * @param EqualityComparerInterface $comparer
     */
    public function __construct($outer, $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector, EqualityComparerInterface $comparer)
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
    public function getIterator()
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
                yield $resultSelector($outerElement, $lookup[$hash]);
            } else {
                yield $resultSelector($outerElement, []);
            }
        }
    }
}
