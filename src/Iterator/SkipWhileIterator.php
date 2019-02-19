<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class SkipWhileIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var callable
     */
    private $predicate;

    /**
     * @param iterable $source
     * @param callable $predicate
     */
    public function __construct($source, callable $predicate)
    {
        $this->source = $source;
        $this->predicate = $predicate;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $predicate = $this->predicate;
        $skipped = false;
        foreach ($this->source as $element) {
            if ($skipped || !$predicate($element)) {
                yield $element;
                $skipped = true;
            }
        }
    }
}
