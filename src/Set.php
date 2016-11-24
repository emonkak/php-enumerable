<?php

namespace Emonkak\Enumerable;

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
     * @param mixed $value
     * @return boolean
     */
    public function add($value)
    {
        $key = $this->hashKey($value);
        if (array_key_exists($key, $this->table)) {
            return false;
        }
        $this->table[$key] = $value;
        return true;
    }

    /**
     * @param array|\Traversable $values
     */
    public function addAll($values)
    {
        foreach ($values as $value) {
            $key = $this->hashKey($value);
            if (array_key_exists($key, $this->table)) {
                continue;
            }
            $this->table[$key] = $value;
        }
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function contains($value)
    {
        $key = $this->hashKey($value);
        return array_key_exists($key, $this->table);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function remove($value)
    {
        $key = $this->hashKey($value);
        if (!array_key_exists($key, $this->table)) {
            return false;
        }
        unset($this->table[$key]);
        return true;
    }

    /**
     * @return mixed[]
     */
    public function toArray()
    {
        return array_values($this->table);
    }

    /**
     * Calculates a hash key for a value.
     *
     * @param mixed $value
     * @return string
     */
    protected function hashKey($value)
    {
        $type = gettype($value);
        switch ($type) {
            case 'boolean':
                return 'b' . $value;

            case 'integer':
                return 'i' . $value;

            case 'double':
                return 'd' . $value;

            case 'string':
                $len = strlen($value);
                if ($len < 256) {
                    return 's' . $len . $value;
                } else {
                    return 's' . $len . sha1($value);
                }

            case 'array':
                // XXX: A different hash is calculated if the order of the keys is different.
                return 'a' . sha1(serialize($value));

            case 'object':
                return 'o' . spl_object_hash($value);

            case 'NULL':
                return 'n';

            default:
                throw new \UnexpectedValueException("The value does not be hashable. got '$type'");
        }
    }
}
