<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class DistinctUntilChangedIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var callable
     */
    private $keySelector;

    /**
     * @param array|\Traversable $source
     * @param callable           $keySelector
     */
    public function __construct($source, callable $keySelector)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $hasCurrentKey = false;
        $currentKey = null;
        $keySelector = $this->keySelector;

        foreach ($this->source as $element) {
            $key = $keySelector($element);
            if (!$hasCurrentKey || $currentKey !== $key) {
                $hasCurrentKey = true;
                $currentKey = $key;
                yield $element;
            }
        }
    }
}
