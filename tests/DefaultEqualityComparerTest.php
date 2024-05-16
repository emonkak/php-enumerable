<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\DefaultEqualityComparer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultEqualityComparer::class)]
class DefaultEqualityComparerTest extends TestCase
{
    #[DataProvider('providerEquals')]
    public function testEquals(mixed $first, mixed $second, bool $expectedResult): void
    {
        $comparer = DefaultEqualityComparer::getInstance();
        $this->assertSame($expectedResult, $comparer->equals($first, $second));
        if ($expectedResult) {
            $this->assertSame($comparer->hash($first), $comparer->hash($second));
        } else {
            $this->assertNotSame($comparer->hash($first), $comparer->hash($second));
        }
    }

    /**
     * @return array<mixed[]>
     */
    public static function providerEquals(): array
    {
        return [
            ['', null, false],
            [0, false, false],
            [1, true, false],
            ['foo', 'foo', true],
            ['foo', 'bar', false],
            [123, 123, true],
            [123, '123', false],
            [(object) [], (object) [], true],
            [(object) ['foo' => 123], (object) ['foo' => 123], true],
            [(object) ['foo' => 123], (object) ['foo' => '123'], false],
            [(object) [], (object) ['foo' => 123], false],
            [(object) ['foo' => 123], (object) [], false],
            [(object) ['foo' => 123, 'bar' => (object) ['baz' => 456]], (object) ['foo' => 123, 'bar' => (object) ['baz' => 456]], true],
            [(object) ['foo' => 123, 'bar' => (object) ['baz' => 456]], (object) ['foo' => 123, 'bar' => (object) ['baz' => '456']], false],
            [new \DateTime('2000-01-02 03:04:05'), new \DateTime('2000-01-02 03:04:05'), true],
            [new \DateTime('2000-01-02 03:04:05'), new \DateTimeImmutable('2000-01-02 03:04:05'), false],
            [[], [], true],
            [['foo' => 123], ['foo' => 123], true],
            [['foo' => 123], ['foo' => '123'], false],
            [['foo' => 123, 'bar' => ['baz' => 456]], ['foo' => 123, 'bar' => ['baz' => 456]], true],
            [['foo' => 123, 'bar' => ['baz' => 456]], ['foo' => 123, 'bar' => ['baz' => '456']], false],
            [[], ['foo' => '123'], false],
            [['foo' => 123], [], false],
        ];
    }

    public function testHash(): void
    {
        /** @var DefaultEqualityComparer<mixed> */
        $comparer = DefaultEqualityComparer::getInstance();

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

    #[DataProvider('providerHashThrowsUnexpectedValueException')]
    public function testHashThrowsUnexpectedValueException(mixed $value): void
    {
        $this->expectException(\UnexpectedValueException::class);

        DefaultEqualityComparer::getInstance()->hash($value);
    }

    /**
     * @return array<mixed[]>
     */
    public static function providerHashThrowsUnexpectedValueException(): array
    {
        return [
            [STDIN],
        ];
    }
}
