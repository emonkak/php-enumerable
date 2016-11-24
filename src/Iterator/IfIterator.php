<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class IfIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var callable
     */
    private $condition;

    /**
     * @var array|\Traversable
     */
    private $thenSource;

    /**
     * @var array|\Traversable
     */
    private $elseSource;

    /**
     * @param callable           $condition
     * @param array|\Traversable $thenSource
     * @param array|\Traversable $elseSource
     */
    public function __construct(callable $condition, $thenSource, $elseSource)
    {
        $this->condition = $condition;
        $this->thenSource = $thenSource;
        $this->elseSource = $elseSource;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $condition = $this->condition;
        if ($condition()) {
            foreach ($this->thenSource as $element) {
                yield $element;
            }
        } else {
            foreach ($this->elseSource as $element) {
                yield $element;
            }
        }
    }
}
