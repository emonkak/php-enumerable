<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

interface Spy
{
    /**
     * @param mixed[] ...$args
     * @return mixed
     */
    public function __invoke(...$args);
}
