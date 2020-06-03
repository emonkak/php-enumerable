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
class TakeWhileIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @psalm-var iterable<TSource>
     * @var iterable
     */
    private $source;

    /**
     * @psalm-var callable(TSource,array-key):bool
     * @var callable
     */
    private $predicate;

    /**
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource,array-key):bool $predicate
     */
    public function __construct(iterable $source, callable $predicate)
    {
        $this->source = $source;
        $this->predicate = $predicate;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        $predicate = $this->predicate;

        foreach ($this->source as $key => $element) {
            if (!$predicate($element, $key)) {
                break;
            }
            yield $element;
        }
    }
}
