<?php

namespace Emonkak\Enumerable;

class Set implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var EqualityComparerInterface
     */
    private $comparer;

    /**
     * @var array
     */
    private $table = [];

    /**
     * @return Set
     */
    public static function create()
    {
        return new Set(EqualityComparer::getInstance());
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
        foreach ($this->table as $value) {
            yield $value;
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
    public function add($value)
    {
        $hash = $this->comparer->hash($value);
        if (array_key_exists($hash, $this->table)) {
            $other = $this->table[$hash];
            if (!$this->comparer->equals($value, $other)) {
                throw new \RuntimeException(sprintf(
                    'Hash collision detected, between "%s" and "%s"',
                    gettype($value),
                    gettype($other)
                ));
            }
            return false;
        }
        $this->table[$hash] = $value;
        return true;
    }

    /**
     * @param array|\Traversable $values
     */
    public function addAll($values)
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function contains($value)
    {
        $hash = $this->comparer->hash($value);
        return array_key_exists($hash, $this->table);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function remove($value)
    {
        $hash = $this->comparer->hash($value);
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
