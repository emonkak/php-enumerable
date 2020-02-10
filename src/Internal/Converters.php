<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Internal;

final class Converters
{
    /**
     * @template TSource
     * @param iterable<TSource> $source
     * @return TSource[]
     */
    public static function toArray(iterable $source): array
    {
        // @phan-suppress-next-line PhanTypeMismatchArgumentInternal
        return is_array($source) ? $source : iterator_to_array($source, false);
    }

    /**
     * @template TSource
     * @template TElement
     * @param iterable<TSource> $source
     * @param callable(TSource):string $keySelector
     * @param callable(TSource):TElement $elementSelector
     * @return array<TKey,TElement>
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
     * @param iterable<TSource> $source
     * @param callable(TSource):string $keySelector
     * @param callable(TSource):TElement $elementSelector
     * @return array<TKey,TElement[]>
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
     * @param iterable<TSource> $source
     * @return \Iterator<TSource>
     */
    public static function toIterator(iterable $source): \Iterator
    {
        if ($source instanceof \Iterator) {
            return $source;
        }
        if (is_array($source)) {
            return new \ArrayIterator($source);
        }
        // @phan-suppress-next-line PhanTypeMismatchArgumentInternal
        return new \IteratorIterator($source);
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
