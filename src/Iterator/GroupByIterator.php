<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;

class GroupByIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var callable
     */
    private $keySelector;

    /**
     * @var callable
     */
    private $elementSelector;

    /**
     * @var callable
     */
    private $resultSelector;

    /**
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @param iterable $source
     * @param callable $keySelector
     * @param callable $elementSelector
     * @param callable $resultSelector
     * @param EqualityComparerInterface $comparer
     */
    public function __construct($source, callable $keySelector, callable $elementSelector, callable $resultSelector, EqualityComparerInterface $comparer)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->elementSelector = $elementSelector;
        $this->resultSelector = $resultSelector;
        $this->comparer = $comparer;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $keySelector = $this->keySelector;
        $elementSelector = $this->elementSelector;
        $resultSelector = $this->resultSelector;

        $lookup = [];

        foreach ($this->source as $element) {
            $key = $keySelector($element);
            $hash = $this->comparer->hash($key);
            $element = $elementSelector($element);

            if (isset($lookup[$hash])) {
                $lookup[$hash][1][] = $element;
            } else {
                $lookup[$hash] = [$key, [$element]];
            }
        }

        foreach ($lookup as list($key, $elements)) {
            yield $resultSelector($key, $elements);
        }
    }
}
