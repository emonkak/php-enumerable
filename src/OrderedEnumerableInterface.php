<?php

namespace Emonkak\Enumerable;

interface OrderedEnumerableInterface extends EnumerableInterface
{
    /**
     * @param ?callable $keySelector
     * @return OrderedEnumerableInterface
     */
    public function thenBy(callable $keySelector = null);

    /**
     * @param ?callable $keySelector
     * @return OrderedEnumerableInterface
     */
    public function thenByDescending(callable $keySelector = null);
}
