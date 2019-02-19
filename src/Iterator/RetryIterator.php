<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class RetryIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable
     */
    private $source;

    /**
     * @var ?int
     */
    private $retryCount;

    /**
     * @param iterable $source
     * @param ?int $retryCount
     */
    public function __construct($source, $retryCount)
    {
        $this->source = $source;
        $this->retryCount = $retryCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
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
