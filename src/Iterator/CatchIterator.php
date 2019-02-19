<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class CatchIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var callable
     */
    private $handler;

    /**
     * @param iterable $source
     * @param callable $handler
     */
    public function __construct($source, callable $handler)
    {
        $this->source = $source;
        $this->handler = $handler;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        try {
            foreach ($this->source as $element) {
                yield $element;
            }
        } catch (\Exception $e) {
            $handler = $this->handler;
            foreach ($handler($e) as $element) {
                yield $element;
            }
        }
    }
}
