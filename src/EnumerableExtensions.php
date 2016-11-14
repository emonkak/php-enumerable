<?php

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Iterator\GroupJoinIterator;
use Emonkak\Enumerable\Iterator\JoinIterator;
use Emonkak\Enumerable\Iterator\MemoizeIterator;
use Emonkak\Enumerable\Iterator\OuterJoinIterator;
use Emonkak\Enumerable\Iterator\SelectIterator;
use Emonkak\Enumerable\Iterator\WhereIterator;
use Emonkak\Enumerable\Iterator\ConcatIterator;

trait EnumerableExtensions
{
    public function aggregate($seed, callable $func)
    {
        $result = $seed;
        foreach ($this as $element) {
            $result = $func($result, $element);
        }
        return $result;
    }

    public function concat($second)
    {
        return new ConcatIterator($this, $second);
    }

    public function first(callable $predicate = null)
    {
        if ($predicate) {
            foreach ($this as $element) {
                if ($predicate($element)) {
                    return $element;
                }
            }
        } else {
            foreach ($this as $element) {
                return $element;
            }
        }
        throw new \RuntimeException('Sequence contains no elements.');
    }

    public function last(callable $predicate = null)
    {
        if ($predicate) {
            $hasValue = false;
            $value = null;
            foreach ($this as $element) {
                if ($predicate($element)) {
                    $value = $element;
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                return $value;
            }
        } else {
            $hasValue = false;
            $value = null;
            foreach ($this as $element) {
                $hasValue = true;
                $value = $element;
            }
            if ($hasValue) {
                return $element;
            }
        }
        throw new \RuntimeException('Sequence contains no elements.');
    }

    public function join($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector)
    {
        return new JoinIterator($this, $inner, $outerKeySelector, $innerKeySelector, $resultSelector);
    }

    public function groupJoin($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector)
    {
        return new GroupJoinIterator($this, $inner, $outerKeySelector, $innerKeySelector, $resultSelector);
    }

    public function outerJoin($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector)
    {
        return new OuterJoinIterator($this, $inner, $outerKeySelector, $innerKeySelector, $resultSelector);
    }

    public function select(callable $selector)
    {
        return new SelectIterator($this, $selector);
    }

    public function where(callable $predicate)
    {
        return new WhereIterator($this, $predicate);
    }

    public function toArray()
    {
        return iterator_to_array($this, false);
    }

    public function toLookup(callable $keySelector, callable $elementSelector = null)
    {
        $elementSelector = $elementSelector ?: $this->identityFunction();
        $lookup = [];

        foreach ($this as $element) {
            $key = $keySelector($element);
            $element = $elementSelector($element);
            if (isset($lookup[$key])) {
                $lookup[$key][] = $element;
            } else {
                $lookup[$key] = [$element];
            }
        }

        return $lookup;
    }

    public function memoize()
    {
        return new MemoizeIterator($this);
    }

    private function identityFunction()
    {
        static $f;

        if (!isset($f)) {
            $f = function($x) {
                return $x;
            };
        }

        return $f;
    }
}
