<?php

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Enumerable;

class EnumerableTest extends \PHPUnit_Framework_TestCase
{
    public function testFirst()
    {
        $xs = [1, 2, 3, 4];
        $predicate = function($x) {
            return $x % 2 === 0;
        };

        $this->assertSame(1, (new Enumerable($xs))->first());
        $this->assertSame(2, (new Enumerable($xs))->first($predicate));
        $this->assertThrows(function() { (new Enumerable([]))->first(); }, \RuntimeException::class);
        $this->assertThrows(function() use ($predicate) { (new Enumerable([1, 3]))->first($predicate); }, \RuntimeException::class);
    }

    public function testLast()
    {
        $xs = [1, 2, 3, 4];
        $predicate = function($x) {
            return $x % 2 === 1;
        };

        $this->assertSame(4, (new Enumerable($xs))->last());
        $this->assertSame(3, (new Enumerable($xs))->last($predicate));
        $this->assertThrows(function() { (new Enumerable([]))->last(); }, \RuntimeException::class);
        $this->assertThrows(function() use ($predicate) { (new Enumerable([2, 4]))->last($predicate); }, \RuntimeException::class);
    }

    public function testSelect()
    {
        $xs = [1, 2, 3, 4];
        $selector = function($x) {
            return $x * 2;
        };

        $this->assertSame([2, 4, 6, 8], (new Enumerable($xs))->select($selector)->toArray());
        $this->assertSame([], (new Enumerable([]))->select($selector)->toArray());
    }

    public function testWhere()
    {
        $xs = [1, 2, 3, 4];
        $predicate = function($x) {
            return $x % 2 === 0;
        };

        $this->assertSame([2, 4], (new Enumerable($xs))->where($predicate)->toArray());
        $this->assertSame([], (new Enumerable([]))->where($predicate)->toArray());
    }

    private function assertThrows(callable $action, $expectedException = 'Exception')
    {
        try {
            $action();
        } catch (\Exception $e) {
            $this->assertInstanceOf($expectedException, $e);
            return;
        }
        $this->fail("Failed asserting that the action throws '$expectedException'.");
    }
}
