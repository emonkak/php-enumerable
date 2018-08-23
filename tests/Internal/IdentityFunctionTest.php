<?php

namespace Emonkak\Tests\Enumerable\Internal;

use Emonkak\Enumerable\Internal\IdentityFunction;

/**
 * @covers Emonkak\Enumerable\Internal\IdentityFunction
 */
class IdentityFunctionTest extends \PHPUnit_Framework_TestCase
{
    public function testApply()
    {
        $this->assertSame(123, IdentityFunction::apply(123));
    }
}
