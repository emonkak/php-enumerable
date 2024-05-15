<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

interface Spy
{
    public function __invoke(mixed ...$args): mixed;
}
