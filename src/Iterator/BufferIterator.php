<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class BufferIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $skip;

    /**
     * @param iterable $source
     * @param int $count
     * @param int $skip
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
