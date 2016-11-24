<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class StartWithIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var mixed[]
     */
    private $elements;

    /**
     * @param array|\Traversable $source
     * @param mixed[]            $elements
     */
    public function __construct($source, array $elements)
    {
        $this->source = $source;
        $this->elements = $elements;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->elements as $element) {
            yield $element;
        }
        foreach ($this->source as $element) {
            yield $element;
        }
    }
}
