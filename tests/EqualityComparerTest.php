<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\EqualityComparer;
use PHPUnit\Framework\TestCase;

/**
 * @covers Emonkak\Enumerable\EqualityComparer
 */
class EqualityComparerTest extends TestCase
{
    /**
     * @dataProvider providerEquals
     * @param mixed $first
     * @param mixed $second
     * @param bool $expectedResult
     */
    public function testEquals($first, $second, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, EqualityComparer::getInstance()->equals($first, $second));
    }

    /**
     * @return array<mixed[]>
     */
    public function providerEquals(): array
    {
        return [
            ['', null, false],
            [0, false, false],
            [1, true, false],
            ['foo', 'foo', true],
            ['foo', 'bar', false],
            [123, 123, true],
            [123, '123', false],
            [new \stdClass(), new \stdClass(), true],
            [['foo' => 123], ['foo' => 123], true],
            [['foo' => 123], ['foo' => '123'], false],
        ];
    }

    public function testHash(): void
    {
        $comparer = EqualityComparer::getInstance();

        $hashes = [
            $comparer->hash('foo'),
            $comparer->hash('123'),
            $comparer->hash(123),
            $comparer->hash(123.0),
            $comparer->hash(true),
            $comparer->hash(null),
            $comparer->hash((object) ['foo' => 123]),
            $comparer->hash((object) ['foo' => '123']),
            $comparer->hash(['foo' => 123]),
            $comparer->hash(['foo' => '123']),
            $comparer->hash(str_repeat('abracadabra', 100)),
        ];

        $this->assertEquals($hashes, array_unique($hashes));
    }

    /**
     * @dataProvider providerHashThrowsUnexpectedValueException
     * @param mixed $value
     */
    public function testHashThrowsUnexpectedValueException($value): void
    {
        $this->expectException(\UnexpectedValueException::class);

        EqualityComparer::getInstance()->hash($value);
    }

    /**
     * @return array<mixed[]>
     */
    public function providerHashThrowsUnexpectedValueException(): array
    {
        return [
            [STDIN],
        ];
    }
}
