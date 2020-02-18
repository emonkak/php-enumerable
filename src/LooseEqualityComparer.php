<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T of ?scalar
 * @implements EqualityComparerInterface<?scalar>
 */
class LooseEqualityComparer implements EqualityComparerInterface
{
    /**
     * @codeCoverageIgnore
     */
    public static function getInstance(): self
    {
        /** @var ?self */
        static $instance;

        if ($instance === null) {
            $instance = new self();
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
        return $first == $second;
    }

    /**
     * {@inheritDoc}
     */
    public function hash($value): string
    {
        try {
            return (string) $value;
        } catch (\Throwable $e) {
            /** @psalm-var mixed $value */
            $typeOrObject = (is_object($value) ? get_class($value) : gettype($value));
            throw new \UnexpectedValueException("The value does not be hashable. got '$typeOrObject'", 0, $e);
        }
    }
}
