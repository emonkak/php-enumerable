<?php

namespace Emonkak\Enumerable\Internal;

use Emonkak\Enumerable\Exception\MoreThanOneElementException;
use Emonkak\Enumerable\Exception\NoSuchElementException;

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
     * @return MoreThanOneElementException
     */
    public static function moreThanOneMatch()
    {
        return new MoreThanOneElementException('Sequence contains more than one element');
    }

    /**
     * @return NoSuchElementException
     */
    public static function noElements()
    {
        return new NoSuchElementException('Sequence contains no elements');
    }
}
