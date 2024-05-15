<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 * @implements \IteratorAggregate<TSource>
 * @implements EnumerableInterface<TSource>
 */
class MemoizeIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @var ?\Iterator<TSource>
     */
    private ?\Iterator $iterator;

    /**
     * @var TSource[]
     */
    private array $cachedElements = [];

    /**
     * @param \Iterator<TSource> $iterator
     */
    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function getIterator(): \Traversable
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
