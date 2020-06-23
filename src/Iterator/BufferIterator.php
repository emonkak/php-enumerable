<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource[]>
 * @implements EnumerableInterface<TSource[]>
 */
class BufferIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource[]>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
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
     * @psalm-param iterable<TSource> $source
     * @psalm-param int $count
     * @psalm-param int $skip
     */
    public function __construct(iterable $source, int $count, int $skip)
    {
        $this->source = $source;
        $this->count = $count;
        $this->skip = $skip;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        $buffer = [];
        $skips = 0;
        $size = 0;

        foreach ($this->source as $element) {
            if ($size < $this->count) {
                $buffer[] = $element;
                $size++;
            } else {
                if ($skips >= $this->skip) {
                    yield $buffer;
                    if ($size > $this->skip) {
                        $buffer = array_slice($buffer, $this->skip);
                        $buffer[] = $element;
                        $size = $size - $this->skip + 1;
                    } else {
                        $buffer = [$element];
                        $size = 1;
                    }
                    $skips = 0;
                }
            }
            $skips++;
        }

        if ($size > 0) {
            yield $buffer;
        }
    }
}
