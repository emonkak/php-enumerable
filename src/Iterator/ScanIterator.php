<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class ScanIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var mixed
     */
    private $seed;

    /**
     * @var callable
     */
    private $func;

    /**
     * @param iterable $source
     * @param mixed $seed
     * @param callable $func
     */
    public function __construct($source, $seed, callable $func)
    {
        $this->source = $source;
        $this->seed = $seed;
        $this->func = $func;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $result = $this->seed;
        $func = $this->func;
        foreach ($this->source as $element) {
            $result = $func($result, $element);
            yield $result;
        }
    }
}
