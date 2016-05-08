<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class MemoizeIterator extends \ArrayObject implements EnumerableInterface
{
    use EnumerableExtensions;

    private $source;

    private $iterator;

    public function __construct($source)
    {
        parent::__construct();

        $this->source = $source;
    }

    public function getIterator()
    {
        if ($this->iterator === null) {
            $this->iterator = $this->toIterator();
            $this->iterator->rewind();
        }

        foreach ($this->getArrayCopy() as $element) {
            yield $element;
        }

        while ($this->iterator->valid()) {
            $element = $this->iterator->current();
            $this->append($element);
            $this->iterator->next();
            yield $element;
        }
    }

    private function toIterator()
    {
        if ($this->source instanceof \Iterator) {
            return $this->source;
        }
        return is_array($this->source)
            ? new \ArrayIterator($this->source)
            : new \IteratorIterator($this->source);
    }
}
