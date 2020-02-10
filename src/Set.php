<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template TSource
 * @implements EnumerableInterface<TSource>
 */
class Set implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var EqualityComparerInterface<TSource>
     */
    private $comparer;

    /**
     * @var array<string,TSource>
     */
    private $hashTable = [];

    /**
     * @template TSource
     * @return self<TSource>
     */
    public static function create(): self
    {
        return new self(EqualityComparer::getInstance());
    }

    /**
     * @param EqualityComparerInterface<TSource> $comparer
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
     * @return iterable<string,TSource>
     */
    public function getSource(): iterable
    {
        return $this->hashTable;
    }

    /**
     * @param TSource $value
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
     * @param iterable<TSource> $values
     */
    public function addAll(iterable $values): void
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * @param TSource $value
     */
    public function contains($value): bool
    {
        $hash = $this->comparer->hash($value);
        return array_key_exists($hash, $this->hashTable);
    }

    /**
     * @param TSource $value
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
     * @return TSource[]
     */
    public function toArray(): array
    {
        return array_values($this->hashTable);
    }
}
