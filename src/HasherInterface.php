<?php

namespace Emonkak\Enumerable;

interface HasherInterface
{
    /**
     * @param mixed $value
     * @return string
     */
    public function hash($value);
}
