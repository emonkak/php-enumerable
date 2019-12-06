<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class MemoizeIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var ?\Iterator
     */
    private $iterator;

    /**
     * @var mixed[]
     */
    private $cachedElements = [];

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
        foreach ($this->cachedElements as $element) {
            yield $element;
        }

        if ($this->iterator !== null) {
            if (empty($this->cachedElements)) {
                $this->iterator->rewind();
            }

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
