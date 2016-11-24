<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class StaticRepeatIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var mixed
     */
    private $element;

    /**
     * @var integer|null
     */
    private $count;

    /**
     * @param mixed        $element
     * @param integer|null $count
     */
    public function __construct($element, $count)
    {
        $this->element = $element;
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        if ($this->count !== null)  {
            for ($i = $this->count; $i > 0; $i--) {
                yield $this->element;
            }
        } else {
            while (true) {
                yield $this->element;
            }
        }
    }
}
