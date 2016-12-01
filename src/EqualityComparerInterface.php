<?php

namespace Emonkak\Enumerable;

interface EqualityComparerInterface
{
    /**
     * @param mixed $first
     * @param mixed $second
     * @return boolean
     */
    public function equals($first, $second);

    /**
     * Calculates a hash for a value.
     *
     * @param mixed $value
     * @return string
     */
    public function hash($value);
}
