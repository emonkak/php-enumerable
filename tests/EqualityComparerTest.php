<?php

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\EqualityComparer;

class EqualityComparerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerEquals
     */
    public function testEquals($first, $second, $expected)
    {
        $this->assertSame($expected, EqualityComparer::getInstance()->equals($first, $second));
    }

    public function providerEquals()
    {
        return [
            ['foo', 'foo', true],
            ['foo', 'bar', false],
            [123, 123, true],
            [123, '123', false],
            [new \stdClass(), new \stdClass(), false],
            [['foo' => 123], ['foo' => 123], true],
        ];
    }

    /**
     * @dataProvider providerHash
     */
    public function testHash($value)
    {
        $this->assertInternalType('string', EqualityComparer::getInstance()->hash($value));
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
        EqualityComparer::getInstance()->hash($value);
    }

    public function providerHashThrowsUnexpectedValueException()
    {
        return [
            [STDIN],
        ];
    }
}
