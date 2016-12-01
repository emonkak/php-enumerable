<?php

namespace Emonkak\Enumerable;

class Set implements \IteratorAggregate, EnumerableInterface
{
    use EnumerableExtensions;

    /**
     * @var HasherInterface
     */
    private $hasher;

    /**
     * @var array
     */
    private $table = [];

    /**
     * @return Set
     */
    public static function create()
    {
        return new Set(Hasher::getInstance());
    }

    /**
     * @param HasherInterface $hasher
     */
    public function __construct(HasherInterface $hasher)
    {
        $this->hasher = $hasher;
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
        $hash = $this->hasher->hash($value);
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
            $hash = $this->hasher->hash($value);
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
        $hash = $this->hasher->hash($value);
        return array_key_exists($hash, $this->table);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function remove($value)
    {
        $hash = $this->hasher->hash($value);
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
