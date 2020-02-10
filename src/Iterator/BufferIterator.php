<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource[]>
 * @implements EnumerableInterface<TSource[]>
 * @use EnumerableExtensions<TSource[]>
 */
class BufferIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
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
     * @param iterable<TSource> $source
     * @param int $count
     * @param int $skip
     */
    public function __construct(iterable $source, int $count, int $skip)
    {
        $this->source = $source;
        $this->count = $count;
        $this->skip = $skip;
    }

    /**
     * @return \Traversable<TSource[]>
     */
    public function getIterator(): \Traversable
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
