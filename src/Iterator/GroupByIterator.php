<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;
use Emonkak\Enumerable\Internal\Converters;

class GroupByIterator implements \IteratorAggregate, EnumerableInterface
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
     * @var callable
     */
    private $elementSelector;

    /**
     * @var callable
     */
    private $resultSelector;

    /**
     * @param array|\Traversable $source
     * @param callable           $keySelector
     * @param callable           $elementSelector
     * @param callable           $resultSelector
     */
    public function __construct($source, callable $keySelector, callable $elementSelector, callable $resultSelector)
    {
        $this->source = $source;
        $this->keySelector = $keySelector;
        $this->elementSelector = $elementSelector;
        $this->resultSelector = $resultSelector;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $keySelector = $this->keySelector;
        $elementSelector = $this->elementSelector;
        $resultSelector = $this->resultSelector;

        $lookup = Converters::toLookup($this->source, $keySelector, $elementSelector);

        foreach ($lookup as $key => $elements) {
            yield $resultSelector($key, $elements);
        }
    }
}
