<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class RepeatIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array|\Traversable
     */
    private $source;

    /**
     * @var integer
     */
    private $count;

    /**
     * @param array|\Traversable $source
     * @param integer|null       $count
     */
    public function __construct($source, $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        if ($this->count === null)  {
            while (true) {
                foreach ($this->source as $element) {
                    yield $element;
                }
            }
        } else {
            for ($i = $this->count; $i > 0; $i--) {
                foreach ($this->source as $element) {
                    yield $element;
                }
            }
        }
    }
}
