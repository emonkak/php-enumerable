<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Set;

class DistinctIterator implements \IteratorAggregate, EnumerableInterface
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
        $set = new Set();
        $keySelector = $this->keySelector;

        foreach ($this->source as $element) {
            $key = $keySelector($element);
            if (!$set->contains($key)) {
                $set->add($key);
                yield $element;
            }
        }
    }
}
