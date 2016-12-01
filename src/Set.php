<?php

namespace Emonkak\Enumerable;

use Emonkak\Enumerable\Internal\Hasher;

class Set implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var array
     */
    private $table = [];

    /**
     * @param array|\Traversable $values
     * @return Set
     */
    public static function from($values)
    {
        $set = new Set();
        $set->addAll($values);
        return $set;
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
        $hash = Hasher::hash($value);
        if (array_key_exists($hash, $this->table)) {
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
            $hash = Hasher::hash($value);
            if (!array_key_exists($hash, $this->table)) {
                $this->table[$hash] = $value;
            }
        }
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function contains($value)
    {
        $hash = Hasher::hash($value);
        return array_key_exists($hash, $this->table);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function remove($value)
    {
        $hash = Hasher::hash($value);
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
