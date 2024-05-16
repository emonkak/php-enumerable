<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T
 * @implements EqualityComparerInterface<T>
 */
class DefaultEqualityComparer implements EqualityComparerInterface
{
    /**
     * @codeCoverageIgnore
     *
     * @template TStatic
     * @return self<TStatic>
     */
    public static function getInstance(): self
    {
        static $instance = new self();

        return $instance;
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public function equals(mixed $first, mixed $second): bool
    {
        if ($first === $second) {
            return true;
        }
        $firstType = gettype($first);
        $secondType = gettype($second);
        if ($firstType !== $secondType) {
            return false;
        }
        if ($firstType === 'array') {
            return $this->areArraysEqual($first, $second);
        }
        if ($firstType === 'object') {
            if (get_class($first) !== get_class($second)) {
                return false;
            }
            return $this->areArraysEqual((array) $first, (array) $second);
        }
        return false;
    }

    public function hash(mixed $value): string
    {
        $type = gettype($value);
        switch ($type) {
            case 'boolean':
                return 'b' . $value;

            case 'integer':
                return 'i' . $value;

            case 'double':
                return 'd' . $value;

            case 'string':
                if (strlen($value) <= 40) {
                    return 's' . $value;
                } else {
                    return 'h' . sha1($value);
                }

                // no break
            case 'array':
                // XXX: A different hash is calculated if the order of the keys is different.
                return 'a' . sha1(serialize($value));

            case 'object':
                return 'o' . sha1(serialize($value));

            case 'NULL':
                return 'n';

            default:
                throw new \UnexpectedValueException("The value is not hashable, got '$type'.");
        }
    }

    /**
     * @param mixed[] $first
     * @param mixed[] $second
     */
    private function areArraysEqual(array $first, array $second): bool
    {
        foreach ($first as $key => $value) {
            if (!isset($second[$key]) || !$this->equals($value, $second[$key])) {
                return false;
            }
        }
        foreach ($second as $key => $value) {
            if (!isset($first[$key]) || !$this->equals($value, $first[$key])) {
                return false;
            }
        }
        return true;
    }
}
