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
 */
class GroupByIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TResult>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private iterable $source;

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
    private EqualityComparerInterface $comparer;

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

    public function getIterator(): \Traversable
    {
        $keySelector = $this->keySelector;
        $elementSelector = $this->elementSelector;
        $resultSelector = $this->resultSelector;

        /** @var array<string,array{0:TKey,1:TElement[]}> */
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
