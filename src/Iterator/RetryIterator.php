<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

/**
 * @template TSource
 */
class RetryIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable<TSource>
     */
    private $source;

    /**
     * @var ?int
     */
    private $retryCount;

    /**
     * @param iterable<TSource> $source
     * @param ?int $retryCount
     */
    public function __construct(iterable $source, ?int $retryCount)
    {
        $this->source = $source;
        $this->retryCount = $retryCount;
    }

    public function getIterator(): \Traversable
    {
        $retryCount = $this->retryCount !== null ? $this->retryCount : INF;
        $error = null;
        while ($retryCount-- > 0) {
            $error = null;
            try {
                foreach ($this->source as $element) {
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
