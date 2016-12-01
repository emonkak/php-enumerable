<?php

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Set;

/**
 * @covers Emonkak\Enumerable\Set
 */
class SetTest extends \PHPUnit_Framework_TestCase
{
    public function testFrom()
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $longString = str_repeat('abracadabra', 100);
        $elements = ['foo', '123', 123, 123.0, true, null, $obj1, $obj2, ['foo' => 'bar'], $longString];

        $set = Set::from(array_merge($elements, $elements));

        $this->assertSame(10, $set->count());
        $this->assertSame(3, $set->count('is_string'));
        $this->assertEquals($elements, iterator_to_array($set));
        $this->assertEquals($elements, $set->toArray());
    }

    public function testAdd()
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $longString = str_repeat('abracadabra', 100);
        $elements = ['foo', '123', 123, 123.0, true, null, $obj1, $obj2, ['foo' => 'bar'], $longString];

        $set = new Set();
        foreach ($elements as $element) {
            $this->assertFalse($set->contains($element));
            $this->assertTrue($set->add($element));
            $this->assertFalse($set->add($element));
            $this->assertTrue($set->contains($element));
        }

        $this->assertSame(10, $set->count());
        $this->assertSame(3, $set->count('is_string'));
        $this->assertEquals($elements, iterator_to_array($set));
        $this->assertEquals($elements, $set->toArray());
    }

    public function testAddAll()
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $longString = str_repeat('abracadabra', 100);
        $elements = ['foo', '123', 123, 123.0, true, null, $obj1, $obj2, ['foo' => 'bar'], $longString];

        $set = new Set();
        $set->addAll(array_merge($elements, $elements));

        $this->assertSame(10, $set->count());
        $this->assertSame(3, $set->count('is_string'));
        $this->assertEquals($elements, iterator_to_array($set));
        $this->assertEquals($elements, $set->toArray());
    }

    public function testRemove()
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $longString = str_repeat('abracadabra', 100);
        $elements = ['foo', '123', 123, 123.0, true, null, $obj1, $obj2, ['foo' => 'bar'], $longString];

        $set = Set::from($elements);

        $this->assertTrue($set->remove('foo'));
        $this->assertFalse($set->remove('foo'));
        $this->assertEquals(['123', 123, 123.0, true, null, $obj1, $obj2, ['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove('123'));
        $this->assertFalse($set->remove('123'));
        $this->assertEquals([123, 123.0, true, null, $obj1, $obj2, ['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove(123));
        $this->assertFalse($set->remove(123));
        $this->assertEquals([123.0, true, null, $obj1, $obj2, ['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove(123.0));
        $this->assertFalse($set->remove(123.0));
        $this->assertEquals([true, null, $obj1, $obj2, ['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove(true));
        $this->assertFalse($set->remove(true));
        $this->assertEquals([null, $obj1, $obj2, ['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove(null));
        $this->assertFalse($set->remove(null));
        $this->assertEquals([$obj1, $obj2, ['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove($obj1));
        $this->assertFalse($set->remove($obj1));
        $this->assertEquals([$obj2, ['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove($obj2));
        $this->assertFalse($set->remove($obj2));
        $this->assertEquals([['foo' => 'bar'], $longString], iterator_to_array($set));

        $this->assertTrue($set->remove(['foo' => 'bar']));
        $this->assertFalse($set->remove(['foo' => 'bar']));
        $this->assertEquals([$longString], iterator_to_array($set));

        $this->assertTrue($set->remove($longString));
        $this->assertFalse($set->remove($longString));
        $this->assertEquals([], iterator_to_array($set));
    }
}
