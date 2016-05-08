<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class JoinIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    private $outer;

    private $inner;

    private $outerKeySelector;

    private $innerKeySelector;

    private $resultSelector;

    public function __construct($outer, $inner, callable $outerKeySelector, callable $innerKeySelector, callable $resultSelector)
    {
        $this->outer = $outer;
        $this->inner = $inner;
        $this->outerKeySelector = $outerKeySelector;
        $this->innerKeySelector = $innerKeySelector;
        $this->resultSelector = $resultSelector;
    }

    public function getIterator()
    {
        $outerKeySelector = $this->outerKeySelector;
        $innerKeySelector = $this->innerKeySelector;
        $resultSelector = $this->resultSelector;

        $lookup = [];

        foreach ($this->inner as $element) {
            $key = $innerKeySelector($element);
            if (isset($lookup[$key])) {
                $lookup[$key][] = $element;
            } else {
                $lookup[$key] = [$element];
            }
        }

        foreach ($this->outer as $outerElement) {
            $key = $outerKeySelector($outerElement);
            if (isset($lookup[$key])) {
                foreach ($lookup[$key] as $innerElement) {
                    yield $resultSelector($outerElement, $innerElement);
                }
            }
        }
    }
}
