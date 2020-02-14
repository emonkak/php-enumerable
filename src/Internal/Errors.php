<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Internal;

use Emonkak\Enumerable\Exception\MoreThanOneElementException;
use Emonkak\Enumerable\Exception\NoSuchElementException;

final class Errors
{
    public static function argumentOutOfRange(string $parameterName): \OutOfRangeException
    {
        return new \OutOfRangeException("Index was out of range must be nonnegative and less than the size of the collection Parameter name: $parameterName");
    }

    public static function moreThanOneMatch(): MoreThanOneElementException
    {
        return new MoreThanOneElementException('Sequence contains more than one element');
    }

    public static function noElements(): NoSuchElementException
    {
        return new NoSuchElementException('Sequence contains no elements');
    }
}
