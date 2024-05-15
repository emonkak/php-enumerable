<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\LooseEqualityComparer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(LooseEqualityComparer::class)]
class LooseEqualityComparerTest extends TestCase
{
    #[DataProvider('providerEquals')]
    public function testEquals(mixed $first, mixed $second, bool $expectedResult): void
    {
        $comparer = LooseEqualityComparer::getInstance();
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
            [null, null, true],
            [null, true, false],
            [null, false, true],
            [null, '', true],
            [null, '0', false],
            [null, '1', false],
            [null, 0, false],
            [null, 1, false],
            [true, true, true],
            [true, false, false],
            [true, 1, true],
            [true, '1', true],
            [true, 0, false],
            [true, '0', false],
            [false, 1, false],
            [false, '1', false],
            [false, 0, false],
            [false, '0', false],
            [0, 0, true],
            [0, '0', true],
            [0, '0.0', false],
            [0, 0.0, true],
            [0, 1, false],
            [1, '1', true],
            [1, '1.0', false],
            [INF, INF, true],
            [INF, -INF, false],
            [-INF, -INF, true],
            [PHP_INT_MAX, PHP_INT_MAX, true],
            [PHP_INT_MAX, PHP_INT_MIN, false],
            [PHP_INT_MIN, PHP_INT_MIN, true],
        ];
    }

    #[DataProvider('providerEqualsThrowsUnexpectedValueException')]
    public function testEqualsThrowsUnexpectedValueException(mixed $value): void
    {
        $this->expectException(\UnexpectedValueException::class);

        LooseEqualityComparer::getInstance()->equals($value, $value);
    }

    /**
     * @return array<mixed[]>
     */
    public static function providerEqualsThrowsUnexpectedValueException(): array
    {
        return [
            [new \stdClass()],
        ];
    }

    #[DataProvider('providerHash')]
    public function testHash(mixed $value, string $expectedHash): void
    {
        $comparer = LooseEqualityComparer::getInstance();

        $this->assertSame($expectedHash, $comparer->hash($value));
    }

    /**
     * @return array<mixed[]>
     */
    public static function providerHash(): array
    {
        return [
            [null, ''],
            [false, ''],
            [true, '1'],
            [123, '123'],
            [123.0, '123'],
            ['123', '123'],
        ];
    }

    public function testUniqueHash(): void
    {
        /** @var LooseEqualityComparer<mixed> */
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

    #[DataProvider('providerHashThrowsUnexpectedValueException')]
    public function testHashThrowsUnexpectedValueException(mixed $value): void
    {
        $this->expectException(\UnexpectedValueException::class);

        LooseEqualityComparer::getInstance()->hash($value);
    }

    /**
     * @return array<mixed[]>
     */
    public static function providerHashThrowsUnexpectedValueException(): array
    {
        return [
            [new \stdClass()],
        ];
    }
}
