<?php

namespace Emonkak\Enumerable;

class Sequence implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @param array|\Traversable $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->source as $element) {
            yield $element;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSource()
    {
        return $this->source;
    }
}
