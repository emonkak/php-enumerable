<?php

namespace Emonkak\Enumerable\Tests;

interface Spy
{
    public function __invoke(...$args);
}
