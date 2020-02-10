<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\EqualityComparerInterface;

/**
 * @template TSource
 * @template TKey
 * @template TElement
 * @template TResult
 * @implements \IteratorAggregate<TResult>
 * @implements EnumerableInterface<TResult>
 * @use EnumerableExtensions<TResult>
 */
class GroupByIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var callable(TSource):TKey
     */
    private $keySelector;

    /**
     * @var callable(TSource):TElement
     */
    private $elementSelector;

    /**
     * @var callable(TKey,TElement[]):TResult
     */
    private $resultSelector;

    /**
     * @var EqualityComparerInterface<TKey>
     */
    private $comparer;

    /**
     * @param iterable<TSource> $source
     * @param callable(TSource):TKey $keySelector
     * @param callable(TSource):TElement $elementSelector
     * @param callable(TKey,TElement[]):TResult $resultSelector
     * @param EqualityComparerInterface<TKey> $comparer
     */
    public function __construct(iterable $source, callable $keySelector, callable $elementSelector, callable $resultSelector, EqualityComparerInterface $comparer)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->elementSelector = $elementSelector;
        $this->resultSelector = $resultSelector;
        $this->comparer = $comparer;
    }

    /**
     * @return \Traversable<TResult>
     */
    public function getIterator(): \Traversable
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
