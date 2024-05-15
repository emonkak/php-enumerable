<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Internal;

final class IdentityFunction
{
    /**
     * @codeCoverageIgnore
     *
     * @template T
     * @return callable(T):T
     */
    public static function get(): callable
    {
        static $f = null;

        if ($f === null) {
            $f = static function(mixed $x): mixed {
                return $x;
            };
        }

        return $f;
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
