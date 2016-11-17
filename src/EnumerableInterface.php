<?php

namespace Emonkak\Enumerable;

interface EnumerableInterface extends \Traversable
{
    public function aggregate($seed, callable $func);

    public function concat($second);

    public function first(callable $predicate = null);

    public function last(callable $predicate = null);

    public function join($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector);

    public function groupJoin($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector);

    public function outerJoin($inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector);

    public function select(callable $selector);

    public function where(callable $predicate);

    public function toArray();

    public function toLookup(callable $keySelector, callable $elementSelector = null);

    public function memoize();
}
