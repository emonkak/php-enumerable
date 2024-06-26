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
class StaticCatchIterator implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<TSource>
     */
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>[]
     */
    private array $sources;

    /**
     * @param iterable<TSource>[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function getIterator(): \Traversable
    {
        $error = null;
        foreach ($this->sources as $source) {
            $error = null;
            try {
                foreach ($source as $element) {
                    yield $element;
                }
            } catch (\Exception $e) {
                $error = $e;
            }
            if ($error === null) {
                break;
            }
        }
        if ($error !== null) {
            throw $error;
        }
    }
}
