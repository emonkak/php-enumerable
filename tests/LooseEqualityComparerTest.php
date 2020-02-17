<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\LooseEqualityComparer;
use PHPUnit\Framework\TestCase;

/**
 * @covers Emonkak\Enumerable\LooseEqualityComparer
 */
class LooseEqualityComparerTest extends TestCase
{
    /**
     * @dataProvider providerEquals
     * @param mixed $first
     * @param mixed $second
     * @param bool $expectedResult
     */
    public function testEquals($first, $second, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, LooseEqualityComparer::getInstance()->equals($first, $second));
    }

    /**
     * @return array<mixed[]>
     */
    public function providerEquals(): array
    {
        return [
            ['', null, true],
            [0, false, true],
            [1, true, true],
            ['foo', 'foo', true],
            ['foo', 'bar', false],
            [123, 123, true],
            [123, '123', true],
            [new \stdClass(), new \stdClass(), true],
            [['foo' => 123], ['foo' => 123], true],
            [['foo' => 123], ['foo' => '123'], true],
        ];
    }

    /**
     * @dataProvider providerHash
     * @param mixed $value
     */
    public function testHash($value, string $expectedHash): void
    {
        $comparer = LooseEqualityComparer::getInstance();

        $this->assertSame($expectedHash, $comparer->hash($value));
    }

    /**
     * @return array<mixed[]>
     */
    public function providerHash(): array
    {
        return [
            [
                '123',
                '123'
            ],
            [
                123.0,
                '123'
            ],
            [
                123,
                '123'
            ],
            [
                null,
                ''
            ],
            [
                false,
                ''
            ],
            [
                true,
                '1'
            ]
        ];
    }

    public function testUniqueHash(): void
    {
        $comparer = LooseEqualityComparer::getInstance();

        $hashes = [
            $comparer->hash('foo'),
            $comparer->hash('123'),
            $comparer->hash(true),
            $comparer->hash(null),
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

        LooseEqualityComparer::getInstance()->hash($value);
    }

    /**
     * @return array<mixed[]>
     */
    public function providerHashThrowsUnexpectedValueException(): array
    {
        return [
            [[]],
            [new \stdClass()]
        ];
    }
}
