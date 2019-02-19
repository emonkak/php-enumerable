<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Internal\Converters;

class ZipIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $first;

    /**
     * @var iterable
     */
    private $second;

    /**
     * @var callable
     */
    private $resultSelector;

    /**
     * @param iterable $first
     * @param iterable $second
     * @param callable $resultSelector
     */
    public function __construct($first, $second, $resultSelector)
    {
        $this->first = $first;
        $this->second = $second;
        $this->resultSelector = $resultSelector;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $resultSelector = $this->resultSelector;
        $first = Converters::toIterator($this->first);
        $second = Converters::toIterator($this->second);

        $first->rewind();
        $second->rewind();

        while ($first->valid() && $second->valid()) {
            yield $resultSelector($first->current(), $second->current());
            $first->next();
            $second->next();
        }
    }
}
