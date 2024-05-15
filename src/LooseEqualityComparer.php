<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T
 * @implements EqualityComparerInterface<T>
 */
class LooseEqualityComparer implements EqualityComparerInterface
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
        try {
            return (string) $first === (string) $second;
        } catch (\Throwable $e) {
            $firstType = (is_object($first) ? get_class($first) : gettype($first));
            $secondType = (is_object($second) ? get_class($second) : gettype($second));
            throw new \UnexpectedValueException("The value is not comparable, got '$firstType' and '$secondType'.", 0, $e);
        }
    }

    public function hash(mixed $value): string
    {
        try {
            return (string) $value;
        } catch (\Throwable $e) {
            $type = (is_object($value) ? get_class($value) : gettype($value));
            throw new \UnexpectedValueException("The value is not be hashable, got '$type'.", 0, $e);
        }
    }
}
