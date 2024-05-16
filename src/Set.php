<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T
 * @implements \IteratorAggregate<T>
 * @implements EnumerableInterface<T>
 */
class Set implements \IteratorAggregate, EnumerableInterface
{
    /**
     * @use EnumerableExtensions<T>
     */
    use EnumerableExtensions;

    /**
     * @var EqualityComparerInterface<T>
     */
    private EqualityComparerInterface $comparer;

    /**
     * @var array<string,T>
     */
    private array $hashTable = [];

    /**
     * @template TStatic
     * @return self<TStatic>
     */
    public static function create(): self
    {
        return new self(DefaultEqualityComparer::getInstance());
    }

    /**
     * @param EqualityComparerInterface<T> $comparer
     */
    public function __construct(EqualityComparerInterface $comparer)
    {
        $this->comparer = $comparer;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->hashTable as $entry) {
            yield $entry;
        }
    }

    /**
     * @return iterable<T>
     */
    public function getSource(): iterable
    {
        return array_values($this->hashTable);
    }

    /**
     * @param T $value
     */
    public function add(mixed $value): bool
    {
        $hash = $this->comparer->hash($value);
        if (array_key_exists($hash, $this->hashTable)) {
            $other = $this->hashTable[$hash];
            if (!$this->comparer->equals($value, $other)) {
                throw new \RuntimeException(sprintf('Hash collision detected, between "%s" and "%s"', gettype($value), gettype($other)));
            }
            return false;
        }
        $this->hashTable[$hash] = $value;
        return true;
    }

    /**
     * @param T $value
     */
    public function contains(mixed $value): bool
    {
        $hash = $this->comparer->hash($value);
        return array_key_exists($hash, $this->hashTable);
    }

    /**
     * @param T $value
     */
    public function remove(mixed $value): bool
    {
        $hash = $this->comparer->hash($value);
        if (!array_key_exists($hash, $this->hashTable)) {
            return false;
        }
        unset($this->hashTable[$hash]);
        return true;
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return array_values($this->hashTable);
    }
}
