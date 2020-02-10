<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template TElement
 * @template TKey
 */
interface OrderedEnumerableInterface extends EnumerableInterface
{
    /**
     * @template TKey
     * @param ?callable(TElement):TKey $keySelector
     * @return OrderedEnumerableInterface<TElement,TKey>
     */
    public function thenBy(?callable $keySelector = null): OrderedEnumerableInterface;

    /**
     * @template TKey
     * @param ?callable(TElement):TKey $keySelector
     * @return OrderedEnumerableInterface<TElement,TKey>
     */
    public function thenByDescending(?callable $keySelector = null): OrderedEnumerableInterface;
}
