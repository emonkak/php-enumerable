<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\EqualityComparer;
use PHPUnit\Framework\TestCase;

class EqualityComparerTest extends TestCase
{
    /**
     * @dataProvider providerEquals
     * @param mixed $first
     * @param mixed $second
     * @param mixed $expected
     */
    public function testEquals($first, $second, $expected): void
    {
        $this->assertSame($expected, EqualityComparer::getInstance()->equals($first, $second));
    }

    /**
     * @return array<mixed[]>
     */
    public function providerEquals(): array
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
            $comparer->hash(new \stdClass()),
            $comparer->hash(['foo' => 'bar']),
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
