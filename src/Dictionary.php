<?php

namespace Emonkak\Enumerable;

class Dictionary implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    const KEY = 0;
    const VALUE = 1;

    /**
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @var array
     */
    private $table = [];

    /**
     * @return Dictionary
     */
    public static function create()
    {
        return new Dictionary(EqualityComparer::getInstance());
    }

    /**
     * @param EqualityComparerInterface $comparer
     */
    public function __construct(EqualityComparerInterface $comparer)
    {
        $this->comparer = $comparer;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->table as $pair) {
            yield $pair;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSource()
    {
        return $this->table;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function set($key, $value)
    {
        $hash = $this->comparer->hash($key);
        if (array_key_exists($hash, $this->table)) {
            if (!$this->comparer->equals($key, $this->table[$hash][self::KEY])) {
                $other = $this->table[$hash][self::KEY];
                throw new \RuntimeException(sprintf(
                    'Hash collision detected, between "%s" and "%s"',
                    json_encode($value),
                    json_encode($other)
                ));
            }
            return false;
        }
        $this->table[$hash] = [$key, $value];
        return true;
    }

    /**
     * @param mixed $key
     * @return boolean
     */
    public function has($key)
    {
        $hash = $this->comparer->hash($key);
        return array_key_exists($hash, $this->table);
    }

    /**
     * @param mixed $key
     * @param mixed &$value
     * @return mixed
     */
    public function tryGet($key, &$value)
    {
        $hash = $this->comparer->hash($key);
        if (!array_key_exists($hash, $this->table)) {
            return false;
        }
        $value = $this->table[$hash][self::VALUE];
        return true;
    }

    /**
     * @param mixed $key
     * @return boolean
     */
    public function remove($key)
    {
        $hash = $this->comparer->hash($key);
        if (!array_key_exists($hash, $this->table)) {
            return false;
        }
        unset($this->table[$hash]);
        return true;
    }

    /**
     * @return mixed[]
     */
    public function toArray()
    {
        return array_values($this->table);
    }
}
