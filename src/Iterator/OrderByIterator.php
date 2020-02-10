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
 */
class OrderByIterator implements \IteratorAggregate, OrderedEnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TElement>
     */
    private $source;

    /**
     * @var callable(TElement):TKey
     */
    private $keySelector;

    /**
     * @var bool
     */
    private $descending;

    /**
     * @var callable(TElement,TElement):int
     */
    private $parentComparer;

    /**
     * @param iterable<TElement> $source
     * @param callable(TElement):TKey $keySelector
     * @param ?callable(TElement,TElement):int $parentComparer
     */
    public function __construct(iterable $source, callable $keySelector, bool $descending, ?callable $parentComparer = null)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->descending = $descending;
        $this->parentComparer = $parentComparer ?: static function($first, $second) {
            return 0;
        };
    }

    public function getIterator(): \Traversable
    {
        $array = Converters::toArray($this->source);
        $comparer = $this->getComparer();
        usort($array, $comparer);
        return new \ArrayIterator($array);
    }

    public function thenBy(callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $this->getComparer();
        return new OrderByIterator($this->source, $keySelector, false, $comparer);
    }

    public function thenByDescending(callable $keySelector = null): OrderedEnumerableInterface
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $this->getComparer();
        return new OrderByIterator($this->source, $keySelector, true, $comparer);
    }

    /**
     * @return callable(TElement,TElement):int
     */
    private function getComparer(): callable
    {
        $keySelector = $this->keySelector;
        $parentComparer = $this->parentComparer;
        if ($this->descending) {
            return static function($first, $second) use ($keySelector, $parentComparer) {
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
            return static function($first, $second) use ($keySelector, $parentComparer) {
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
