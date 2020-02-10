<?php

declare(strict_types=1);

namespace Emonkak\Tests\Enumerable\Internal;

use Emonkak\Enumerable\Internal\IdentityFunction;
use PHPUnit\Framework\TestCase;

/**
 * @covers Emonkak\Enumerable\Internal\IdentityFunction
 */
class IdentityFunctionTest extends TestCase
{
    public function testApply()
    {
        $this->assertSame(123, IdentityFunction::apply(123));
    }
}
