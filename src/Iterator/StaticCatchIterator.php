<?php

namespace Emonkak\Enumerable\Iterator;

use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\EnumerableInterface;

class StaticCatchIterator implements \IteratorAggregate, EnumerableInterface
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
