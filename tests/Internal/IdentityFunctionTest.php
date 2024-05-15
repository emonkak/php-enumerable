<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests\Internal;

use Emonkak\Enumerable\Internal\IdentityFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IdentityFunction::class)]
class IdentityFunctionTest extends TestCase
{
    public function testGet(): void
    {
        /** @var callable(mixed):mixed */
        $f = IdentityFunction::get();
        $this->assertSame(123, $f(123));
    }
}
