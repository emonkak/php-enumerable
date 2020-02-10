<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T
 */
interface EqualityComparerInterface
{
    /**
     * @param T $first
     * @param T $second
     * @return bool
     */
    public function equals($first, $second): bool;

    /**
     * Calculates a hash for a value.
     *
     * @param T $value
     */
    public function hash($value): string;
}
