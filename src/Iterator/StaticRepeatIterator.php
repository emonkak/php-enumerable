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
class StaticRepeatIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @psalm-var TSource
     * @var mixed
     */
    private $element;

    /**
     * @var ?int
     */
    private $count;

    /**
     * @psalm-param TSource $element
     * @psalm-param ?int $count
     */
    public function __construct($element, ?int $count)
    {
        $this->element = $element;
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
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
