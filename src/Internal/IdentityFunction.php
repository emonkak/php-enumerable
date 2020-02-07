<?php

namespace Emonkak\Enumerable\Internal;

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
