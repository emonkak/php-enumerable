<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\Internal\Converters;
use Emonkak\Enumerable\Internal\IdentityFunction;
use Emonkak\Enumerable\OrderedEnumerableInterface;

/**
 * @template TElement
 * @template TKey
 * @implements \IteratorAggregate<TElement>
 * @implements OrderedEnumerableInterface<TElement,TKey>
 */
class OrderByIterator implements \IteratorAggregate, OrderedEnumerableInterface
{
    /**
     * @use EnumerableExtensions<TElement>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TElement>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable(TElement):TKey
     * @var callable
     */
    private $keySelector;

    /**
     * @var bool
     */
    private $descending;

    /**
     * @psalm-var callable(TElement,TElement):int
     * @var callable
     */
    private $parentComparer;

    /**
     * @psalm-param iterable<TElement> $source
     * @psalm-param callable(TElement):TKey $keySelector
     * @psalm-param ?callable(TElement,TElement):int $parentComparer
     */
    public function __construct(iterable $source, callable $keySelector, bool $descending, ?callable $parentComparer = null)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->descending = $descending;
        $this->parentComparer = $parentComparer ?:
            /**
             * @psalm-param TElement $first
             * @psalm-param TElement $second
             */
            static function($first, $second): int {
                return 0;
            };
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        $array = Converters::toArray($this->source);
        $comparer = $this->getComparer();
        usort($array, $comparer);
        return new \ArrayIterator($array);
    }

    /**
     * @template TNextKey
     * @psalm-param ?callable(TElement):TNextKey $keySelector
     * @psalm-return OrderedEnumerableInterface<TElement,TNextKey>
     */
    public function thenBy(callable $keySelector = null): OrderedEnumerableInterface
    {
        /** @psalm-var callable(TElement):TNextKey */
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $this->getComparer();
        return new OrderByIterator($this->source, $keySelector, false, $comparer);
    }

    /**
     * @template TNextKey
     * @psalm-param ?callable(TElement):TNextKey $keySelector
     * @psalm-return OrderedEnumerableInterface<TElement,TNextKey>
     */
    public function thenByDescending(callable $keySelector = null): OrderedEnumerableInterface
    {
        /** @psalm-var callable(TElement):TNextKey */
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $this->getComparer();
        return new OrderByIterator($this->source, $keySelector, true, $comparer);
    }

    /**
     * @psalm-return callable(TElement,TElement):int
     */
    private function getComparer(): callable
    {
        $keySelector = $this->keySelector;
        $parentComparer = $this->parentComparer;
        if ($this->descending) {
            return
                /**
                 * @psalm-param TElement $first
                 * @psalm-param TElement $second
                 */
                static function($first, $second) use ($keySelector, $parentComparer): int {
                    $ordering = $parentComparer($first, $second);
                    if ($ordering != 0) {
                        return $ordering;
                    }
                    $firstKey = $keySelector($first);
                    $secondKey = $keySelector($second);
                    if ($firstKey == $secondKey) {
                        return 0;
                    }
                    return $firstKey < $secondKey ? 1 : -1;
                };
        } else {
            return
                /**
                 * @psalm-param TElement $first
                 * @psalm-param TElement $second
                 */
                static function($first, $second) use ($keySelector, $parentComparer): int {
                    $ordering = $parentComparer($first, $second);
                    if ($ordering != 0) {
                        return $ordering;
                    }
                    $firstKey = $keySelector($first);
                    $secondKey = $keySelector($second);
                    if ($firstKey == $secondKey) {
                        return 0;
                    }
                    return $firstKey < $secondKey ? -1 : 1;
                };
        }
    }
}
