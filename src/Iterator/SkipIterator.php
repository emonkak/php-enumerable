<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 * @use EnumerableExtensions<TSource>
 */
class SkipIterator implements \IteratorAggregate, EnumerableInterface
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
     * @param iterable<TSource> $source
     * @param int $count
     */
    public function __construct(iterable $source, int $count)
    {
        $this->source = $source;
        $this->count = $count;
    }

    /**
     * @return \Traversable<TSource>
     */
    public function getIterator(): \Traversable
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
