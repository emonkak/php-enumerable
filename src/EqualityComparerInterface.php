<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T
 */
interface EqualityComparerInterface
{
    /**
     * @psalm-param T $first
     * @psalm-param T $second
     * @psalm-return bool
     */
    public function equals($first, $second): bool;

    /**
     * Calculates a hash for a value.
     *
     * @psalm-param T $value
     */
    public function hash($value): string;
}
