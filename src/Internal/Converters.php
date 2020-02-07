<?php

namespace Emonkak\Enumerable\Internal;

final class Converters
{
    /**
     * @param iterable $source
     * @return mixed[]
     */
    public static function toArray($source)
    {
        return is_array($source) ? $source : iterator_to_array($source, false);
    }

    /**
     * @param iterable $source
     * @param callable $keySelector
     * @param callable $elementSelector
     * @return array
     */
    public static function toDictionary($source, callable $keySelector, callable $elementSelector)
    {
        $dict = [];
        foreach ($source as $element) {
            $dict[$keySelector($element)] = $elementSelector($element);
        }
        return $dict;
    }

    /**
     * @param iterable $source
     * @param callable $keySelector
     * @param callable $elementSelector
     * @return array
     */
    public static function toLookup($source, callable $keySelector, callable $elementSelector)
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
     * @param iterable $source
     * @return \Iterator
     */
    public static function toIterator($source)
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
