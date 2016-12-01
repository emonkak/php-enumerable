<?php

namespace Emonkak\Enumerable\Internal;

/**
 * @internal
 */
final class Errors
{
    /**
     * @param string $s
     * @return \OutOfRangeException
     */
    public static function argumentOutOfRange($s)
    {
        return new \OutOfRangeException("Index was out of range must be nonnegative and less than the size of the collection Parameter name: $s");
    }

    /**
     * @return \RuntimeException
     */
    public static function moreThanOneMatch()
    {
        return new \RuntimeException('More than one match found.');
    }

    /**
     * @return \RuntimeException
     */
    public static function noElements()
    {
        return new \RuntimeException('Sequence contains no elements.');
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
