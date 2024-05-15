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
     */
    public function equals(mixed $first, mixed $second): bool;

    /**
     * Calculates a hash for a value.
     *
     * @param T $value
     */
    public function hash(mixed $value): string;
}
