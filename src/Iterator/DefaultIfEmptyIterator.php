<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class DefaultIfEmptyIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * @param array|\Traversable $source
     * @param mixed              $defaultValue
     */
    public function __construct($source, $defaultValue)
    {
        $this->source = $source;
        $this->defaultValue = $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $hasValue = false;

        foreach ($this->source as $element) {
            yield $element;
            $hasValue = true;
        }

        if (!$hasValue) {
            yield $this->defaultValue;
        }
    }
}
