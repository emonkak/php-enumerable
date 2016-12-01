<?php

namespace Emonkak\Enumerable;

/**
 * @internal
 */
class Hasher implements HasherInterface
{
    /**
     * @codeCoverageIgnore
     *
     * @return Hasher
     */
    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Hasher();
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
     * Calculates a hash for a value.
     *
     * @param mixed $value
     * @return string
     */
    public function hash($value)
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
