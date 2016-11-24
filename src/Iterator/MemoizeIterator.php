<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class MemoizeIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var \Iterator|null
     */
    private $iterator;

    /**
     * @var mixed[]|null
     */
    private $cachedElements;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        if ($this->cachedElements === null) {
            $this->cachedElements = [];
            $this->iterator->rewind();
        }

        foreach ($this->cachedElements as $element) {
            yield $element;
        }

        if ($this->iterator !== null) {
            while ($this->iterator->valid()) {
                $element = $this->iterator->current();
                $this->cachedElements[] = $element;
                $this->iterator->next();
                yield $element;
            }

            $this->iterator = null;
        }
    }
}
