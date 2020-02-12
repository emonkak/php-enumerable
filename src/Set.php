<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template T
 * @implements \IteratorAggregate<T>
 * @implements EnumerableInterface<T>
 * @use EnumerableExtensions<T>
 */
class Set implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var EqualityComparerInterface<T>
     */
    private $comparer;

    /**
     * @var array<string,T>
     */
    private $hashTable = [];

    /**
     * @return self<T>
     */
    public static function create(): self
    {
        return new self(EqualityComparer::getInstance());
    }

    /**
     * @param EqualityComparerInterface<T> $comparer
     */
    public function __construct(EqualityComparerInterface $comparer)
    {
        $this->comparer = $comparer;
    }

    /**
     * @return \Traversable<T>
     */
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
    public function add($value): bool
    {
        $hash = $this->comparer->hash($value);
        if (array_key_exists($hash, $this->hashTable)) {
            $other = $this->hashTable[$hash];
            if (!$this->comparer->equals($value, $other)) {
                throw new \RuntimeException(sprintf(
                    'Hash collision detected, between "%s" and "%s"',
                    gettype($value),
                    gettype($other)
                ));
            }
            return false;
        }
        $this->hashTable[$hash] = $value;
        return true;
    }

    /**
     * @param T $value
     */
    public function contains($value): bool
    {
        $hash = $this->comparer->hash($value);
        return array_key_exists($hash, $this->hashTable);
    }

    /**
     * @param T $value
     */
    public function remove($value): bool
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
