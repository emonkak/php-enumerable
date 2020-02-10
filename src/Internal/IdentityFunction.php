<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Internal;

final class IdentityFunction
{
    /**
     * @template T
     * @param T $x
     * @return T
     */
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
