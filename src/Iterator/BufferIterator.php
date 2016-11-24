<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class BufferIterator implements \IteratorAggregate, EnumerableInterface
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
     * @var integer
     */
    private $skip;

    /**
     * @param array|\Traversable $source
     * @param integer            $count
     * @param integer            $skip
     */
    public function __construct($source, $count, $skip)
    {
        $this->source = $source;
        $this->count = $count;
        $this->skip = $skip;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $buffer = [];
        $skipped = 0;

        foreach ($this->source as $element) {
            if (count($buffer) < $this->count) {
                $buffer[] = $element;
            } else {
                if ($skipped >= $this->skip) {
                    yield $buffer;
                    $buffer = array_slice($buffer, $this->skip);
                    $buffer[] = $element;
                    $skipped = 0;
                }
            }
            $skipped++;
        }

        if (count($buffer) > 0) {
            yield $buffer;
        }
    }
}
