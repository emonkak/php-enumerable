<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\EqualityComparer;
use PHPUnit\Framework\TestCase;

class EqualityComparerTest extends TestCase
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
            [new \stdClass(), new \stdClass(), true],
            [['foo' => 123], ['foo' => 123], true],
        ];
    }

    public function testHash()
    {
        $comparer = EqualityComparer::getInstance();

        $hashes = [
            $comparer->hash('foo'),
            $comparer->hash('123'),
            $comparer->hash(123),
            $comparer->hash(123.0),
            $comparer->hash(true),
            $comparer->hash(null),
            $comparer->hash(new \stdClass()),
            $comparer->hash(['foo' => 'bar']),
            $comparer->hash(str_repeat('abracadabra', 100)),
        ];

        $this->assertEquals($hashes, array_unique($hashes));
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
