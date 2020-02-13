<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @implements EqualityComparerInterface<mixed>
 */
class EqualityComparer implements EqualityComparerInterface
{
    /**
     * @codeCoverageIgnore
     */
    public static function getInstance(): self
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new EqualityComparer();
        }

        return $instance;
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function equals($first, $second): bool
    {
        return is_scalar($first) ? $first === $second : $first == $second;
    }

    /**
     * {@inheritDoc}
     */
    public function hash($value): string
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

            case 'array':
                // XXX: A different hash is calculated if the order of the keys is different.
                return 'a' . sha1(serialize($value));

            case 'object':
                return 'o' . sha1(serialize($value));

            case 'NULL':
                return 'n';

            default:
                throw new \UnexpectedValueException("The value does not be hashable. got '$type'");
        }
    }
}
