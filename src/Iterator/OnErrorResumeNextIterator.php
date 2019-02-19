<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class OnErrorResumeNextIterator implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var iterable[]
     */
    private $sources;

    /**
     * @param iterable[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->sources as $source) {
            try {
                foreach ($source as $element) {
                    yield $element;
                }
            } catch (\Exception $e) {
            }
        }
    }
}
