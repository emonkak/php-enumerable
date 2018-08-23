<?php

namespace Emonkak\Enumerable\Internal;

/**
 * @internal
 */
final class IdentityFunction
{
    public static function apply($x)
    {
        return $x;
    }

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
    }
}
