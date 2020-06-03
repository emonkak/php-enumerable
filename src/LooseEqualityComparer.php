<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
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
     * {@inheritdoc}
     */
    public function equals($first, $second): bool
    {
        try {
            return (string) $first === (string) $second;
        } catch (\Throwable $e) {
            /**
             * @psalm-var mixed $first
             * @psalm-var mixed $second
             */
            $firstType = (is_object($first) ? get_class($first) : gettype($first));
            $secondType = (is_object($second) ? get_class($second) : gettype($second));
            throw new \UnexpectedValueException("The value does not be comparable. between '$firstType' and '$secondType'.", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hash($value): string
    {
        try {
            return (string) $value;
        } catch (\Throwable $e) {
            /** @psalm-var mixed $value */
            $typeOrObject = (is_object($value) ? get_class($value) : gettype($value));
            throw new \UnexpectedValueException("The value does not be hashable. got '$typeOrObject'.", 0, $e);
        }
    }
}
