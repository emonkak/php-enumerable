<?php

declare(strict_types=1);

namespace Emonkak\Enumerable;

/**
 * @template TKey
 * @template TValue
 * @implements \IteratorAggregate<array{0:TKey,1:TValue}>
 * @implements EnumerableInterface<array{0:TKey,1:TValue}>
 * @use EnumerableExtensions<array{0:TKey,1:TValue}>
 */
class Dictionary implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    const KEY = 0;
    const VALUE = 1;

    /**
     * @psalm-var EqualityComparerInterface<TKey>
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @psalm-var array{0:TKey,1:TValue}[]
     * @var array
     */
    private $hashTable = [];

    /**
     * @template TCreateKey
     * @template TCreateValue
     * @psalm-return self<TCreateKey,TCreateValue>
     */
    public static function create(): self
    {
        return new self(EqualityComparer::getInstance());
    }

    /**
     * @psalm-param EqualityComparerInterface<TKey> $comparer
     */
    public function __construct(EqualityComparerInterface $comparer)
    {
        $this->comparer = $comparer;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->hashTable as $entry) {
            yield $entry;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSource(): iterable
    {
        return array_values($this->hashTable);
    }

    /**
     * @psalm-param TKey $key
     * @psalm-param TValue $value
     */
    public function set($key, $value): bool
    {
        $hash = $this->comparer->hash($key);
        if (array_key_exists($hash, $this->hashTable)) {
            if (!$this->comparer->equals($key, $this->hashTable[$hash][self::KEY])) {
                $other = $this->hashTable[$hash][self::KEY];
                throw new \RuntimeException(sprintf(
                    'Hash collision detected, between "%s" and "%s"',
                    gettype($value),
                    gettype($other)
                ));
            }
            return false;
        }
        $this->hashTable[$hash] = [$key, $value];
        return true;
    }

    /**
     * @psalm-param TKey $key
     */
    public function has($key): bool
    {
        $hash = $this->comparer->hash($key);
        return array_key_exists($hash, $this->hashTable);
    }

    /**
     * @psalm-param TKey $key
     * @psalm-param TValue $value
     * @psalm-return bool
     */
    public function tryGet($key, &$value): bool
    {
        $hash = $this->comparer->hash($key);
        if (!array_key_exists($hash, $this->hashTable)) {
            return false;
        }
        $value = $this->hashTable[$hash][self::VALUE];
        return true;
    }

    /**
     * @psalm-param TKey $key
     */
    public function remove($key): bool
    {
        $hash = $this->comparer->hash($key);
        if (!array_key_exists($hash, $this->hashTable)) {
            return false;
        }
        unset($this->hashTable[$hash]);
        return true;
    }

    /**
     * @psalm-return array{0:TKey,1:TValue}[]
     */
    public function toArray(): array
    {
        return array_values($this->hashTable);
    }
}
