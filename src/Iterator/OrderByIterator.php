<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\Internal\Converters;
use Emonkak\Enumerable\Internal\IdentityFunction;
use Emonkak\Enumerable\OrderedEnumerableInterface;

class OrderByIterator implements \IteratorAggregate, OrderedEnumerableInterface
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
     * @var bool
     */
    private $descending;

    /**
     * @var callable
     */
    private $parentComparer;

    /**
     * @param iterable $source
     * @param callable $keySelector
     * @param bool $descending
     * @param ?callable $parentComparer
     */
    public function __construct($source, callable $keySelector, $descending, callable $parentComparer = null)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->descending = $descending;
        $this->parentComparer = $parentComparer ?: static function($first, $second) {
            return 0;
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $array = Converters::toArray($this->source);
        $comparer = $this->getComparer();
        usort($array, $comparer);
        return new \ArrayIterator($array);
    }

    /**
     * {@inheritDoc}
     */
    public function thenBy(callable $keySelector = null)
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $this->getComparer();
        return new OrderByIterator($this->source, $keySelector, false, $comparer);
    }

    /**
     * {@inheritDoc}
     */
    public function thenByDescending(callable $keySelector = null)
    {
        $keySelector = $keySelector ?: [IdentityFunction::class, 'apply'];
        $comparer = $this->getComparer();
        return new OrderByIterator($this->source, $keySelector, true, $comparer);
    }

    /**
     * @return callable
     */
    private function getComparer()
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
