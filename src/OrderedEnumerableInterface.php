<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template TElement
 * @template TKey
 * @extends EnumerableInterface<TElement>
 */
interface OrderedEnumerableInterface extends EnumerableInterface
{
    /**
     * @template TNextKey
     * @psalm-param callable(TElement):TNextKey|null $keySelector
     * @psalm-return OrderedEnumerableInterface<TElement,TNextKey>
     */
    public function thenBy(?callable $keySelector = null): OrderedEnumerableInterface;

    /**
     * @template TNextKey
     * @psalm-param callable(TElement):TNextKey|null $keySelector
     * @psalm-return OrderedEnumerableInterface<TElement,TNextKey>
     */
    public function thenByDescending(?callable $keySelector = null): OrderedEnumerableInterface;
}
