<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class SkipIterator implements \IteratorAggregate, EnumerableInterface
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
     * @param integer            $count
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
        if (is_array($this->source)) {
            $count = $this->count;
            $length = count($this->source);
            $elements = array_values($this->source);
            while ($count < $length) {
                yield $elements[$count++];
            }
        } else {
            $count = $this->count;
            foreach ($this->source as $element) {
                if ($count > 0) {
                    $count--;
                } else {
                    yield $element;
                }
            }
        }
    }
}
