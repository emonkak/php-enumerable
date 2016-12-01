<?php

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Hasher;

class HasherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerHash
     */
    public function testHash($value)
    {
        $this->assertInternalType('string', Hasher::getInstance()->hash($value));
    }

    public function providerHash()
    {
        return [
            ['foo'],
            ['123'],
            [123],
            [123.0],
            [true],
            [null],
            [new \stdClass()],
            [['foo' => 'bar']],
            [str_repeat('abracadabra', 100)],
        ];
    }

    /**
     * @expectedException UnexpectedValueException
     * 
     * @dataProvider providerHashThrowsUnexpectedValueException
     */
    public function testHashThrowsUnexpectedValueException($value)
    {
        Hasher::getInstance()->hash($value);
    }

    public function providerHashThrowsUnexpectedValueException()
    {
        return [
            [STDIN],
        ];
    }
}
