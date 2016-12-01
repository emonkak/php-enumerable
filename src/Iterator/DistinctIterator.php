<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\HasherInterface;
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
     * @var HasherInterface
     */
    private $hasher;

    /**
     * @param array|\Traversable $source
     * @param callable           $keySelector
     * @param HasherInterface    $hasher
     */
    public function __construct($source, callable $keySelector, HasherInterface $hasher)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->hasher = $hasher;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $set = new Set($this->hasher);
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
