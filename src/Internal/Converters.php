<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Internal;

final class Converters
{
    /**
     * @template TSource
     * @psalm-param iterable<TSource> $source
     * @psalm-return TSource[]
     */
    public static function toArray(iterable $source): array
    {
        return is_array($source) ? $source : iterator_to_array($source, false);
    }

    /**
     * @template TSource
     * @template TElement
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource):array-key $keySelector
     * @psalm-param callable(TSource):TElement $elementSelector
     * @psalm-return array<array-key,TElement>
     */
    public static function toDictionary(iterable $source, callable $keySelector, callable $elementSelector): array
    {
        $dict = [];
        foreach ($source as $element) {
            $dict[$keySelector($element)] = $elementSelector($element);
        }
        return $dict;
    }

    /**
     * @template TSource
     * @template TElement
     * @psalm-param iterable<TSource> $source
     * @psalm-param callable(TSource):array-key $keySelector
     * @psalm-param callable(TSource):TElement $elementSelector
     * @psalm-return array<array-key,TElement[]>
     */
    public static function toLookup(iterable $source, callable $keySelector, callable $elementSelector): array
    {
        $lookup = [];

        foreach ($source as $element) {
            $key = $keySelector($element);
            $element = $elementSelector($element);
            $lookup[$key][] = $element;
        }

        return $lookup;
    }

    /**
     * @template TSource
     * @psalm-param iterable<TSource> $source
     * @psalm-return \Iterator<TSource>
     */
    public static function toIterator(iterable $source): \Iterator
    {
        if ($source instanceof \Iterator) {
            return $source;
        }
        if (is_array($source)) {
            return new \ArrayIterator($source);
        }
        return new \IteratorIterator($source);
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
