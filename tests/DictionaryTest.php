<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Dictionary;
use Emonkak\Enumerable\EqualityComparerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers Emonkak\Enumerable\Dictionary
 */
class DictionaryTest extends TestCase
{
    public function testCreate(): void
    {
        $obj = new \stdClass();

        /** @var Dictionary<mixed,mixed> */
        $dict = Dictionary::create();

        $this->assertTrue($dict->set('foo', 123));
        $this->assertTrue($dict->set('bar', 456));

        $this->assertSame(2, $dict->count());
        $this->assertTrue($dict->has('foo'));
        $this->assertTrue($dict->tryGet('foo', $value));
        $this->assertSame(123, $value);
        $this->assertTrue($dict->has('bar'));
        $this->assertTrue($dict->tryGet('bar', $value));
        $this->assertSame(456, $value);
        $this->assertFalse($dict->has('baz'));
        $this->assertFalse($dict->tryGet('baz', $value));

        $this->assertFalse($dict->set('foo', 123));
        $this->assertFalse($dict->set('bar', 456));
        $this->assertTrue($dict->set(1, 'foo'));
        $this->assertTrue($dict->set('1', 'bar'));
        $this->assertTrue($dict->set($obj, 'baz'));

        $this->assertSame(5, $dict->count());
        $this->assertTrue($dict->tryGet(1, $value));
        $this->assertSame('foo', $value);
        $this->assertTrue($dict->tryGet('1', $value));
        $this->assertSame('bar', $value);
        $this->assertTrue($dict->tryGet($obj, $value));
        $this->assertSame('baz', $value);

        $this->assertTrue($dict->remove('foo'));
        $this->assertTrue($dict->remove('bar'));
        $this->assertFalse($dict->remove('baz'));

        $this->assertSame(3, $dict->count());
        $this->assertFalse($dict->has('foo'));
        $this->assertFalse($dict->tryGet('bar', $value));
        $this->assertFalse($dict->has('bar'));
        $this->assertFalse($dict->tryGet('bar', $value));

        $expectedValues = [[1, 'foo'], ['1', 'bar'], [$obj, 'baz']];
        $this->assertEquals($expectedValues, $dict->toArray());
        $this->assertEquals($expectedValues, iterator_to_array($dict));
    }

    public function testAddWithHashCollision(): void
    {
        $this->expectException(\RuntimeException::class);

        $comparer = $this->createMock(EqualityComparerInterface::class);
        $comparer
            ->expects($this->any())
            ->method('hash')
            ->willReturn('0');
        $comparer
            ->expects($this->any())
            ->method('equals')
            ->will($this->returnCallback(function($first, $second) {
                return $first === $second;
            }));

        $dict = new Dictionary($comparer);
        $dict->set('foo', 123);
        $dict->set('bar', 456);
    }
}
