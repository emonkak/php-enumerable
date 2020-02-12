<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Enumerable;
use Emonkak\Enumerable\EnumerableInterface;
use PHPUnit\Exception as PHPUnitException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Emonkak\Enumerable\Enumerable
 * @covers Emonkak\Enumerable\EnumerableExtensions
 * @covers Emonkak\Enumerable\Internal\Converters
 * @covers Emonkak\Enumerable\Internal\Errors
 * @covers Emonkak\Enumerable\Iterator\BufferIterator
 * @covers Emonkak\Enumerable\Iterator\CatchIterator
 * @covers Emonkak\Enumerable\Iterator\ConcatIterator
 * @covers Emonkak\Enumerable\Iterator\ConcatIterator
 * @covers Emonkak\Enumerable\Iterator\DefaultIfEmptyIterator
 * @covers Emonkak\Enumerable\Iterator\DeferIterator
 * @covers Emonkak\Enumerable\Iterator\DistinctIterator
 * @covers Emonkak\Enumerable\Iterator\DistinctUntilChangedIterator
 * @covers Emonkak\Enumerable\Iterator\DoIterator
 * @covers Emonkak\Enumerable\Iterator\DoWhileIterator
 * @covers Emonkak\Enumerable\Iterator\EmptyIterator
 * @covers Emonkak\Enumerable\Iterator\EmptyIterator
 * @covers Emonkak\Enumerable\Iterator\ExceptIterator
 * @covers Emonkak\Enumerable\Iterator\FinallyIterator
 * @covers Emonkak\Enumerable\Iterator\GenerateIterator
 * @covers Emonkak\Enumerable\Iterator\GroupByIterator
 * @covers Emonkak\Enumerable\Iterator\GroupJoinIterator
 * @covers Emonkak\Enumerable\Iterator\IfIterator
 * @covers Emonkak\Enumerable\Iterator\IntersectIterator
 * @covers Emonkak\Enumerable\Iterator\JoinIterator
 * @covers Emonkak\Enumerable\Iterator\MemoizeIterator
 * @covers Emonkak\Enumerable\Iterator\OnErrorResumeNextIterator
 * @covers Emonkak\Enumerable\Iterator\OnErrorResumeNextIterator
 * @covers Emonkak\Enumerable\Iterator\OrderByIterator
 * @covers Emonkak\Enumerable\Iterator\OuterJoinIterator
 * @covers Emonkak\Enumerable\Iterator\RangeIterator
 * @covers Emonkak\Enumerable\Iterator\RepeatIterator
 * @covers Emonkak\Enumerable\Iterator\RetryIterator
 * @covers Emonkak\Enumerable\Iterator\ReverseIterator
 * @covers Emonkak\Enumerable\Iterator\ScanIterator
 * @covers Emonkak\Enumerable\Iterator\SelectIterator
 * @covers Emonkak\Enumerable\Iterator\SelectManyIterator
 * @covers Emonkak\Enumerable\Iterator\SkipIterator
 * @covers Emonkak\Enumerable\Iterator\SkipLastIterator
 * @covers Emonkak\Enumerable\Iterator\SkipWhileIterator
 * @covers Emonkak\Enumerable\Iterator\StartWithIterator
 * @covers Emonkak\Enumerable\Iterator\StaticCatchIterator
 * @covers Emonkak\Enumerable\Iterator\StaticRepeatIterator
 * @covers Emonkak\Enumerable\Iterator\TakeIterator
 * @covers Emonkak\Enumerable\Iterator\TakeLastIterator
 * @covers Emonkak\Enumerable\Iterator\TakeWhileIterator
 * @covers Emonkak\Enumerable\Iterator\UnionIterator
 * @covers Emonkak\Enumerable\Iterator\WhereIterator
 * @covers Emonkak\Enumerable\Iterator\WhileIterator
 * @covers Emonkak\Enumerable\Iterator\ZipIterator
 * @covers Emonkak\Enumerable\Iterator\ZipIterator
 */
class EnumerableTest extends TestCase
{
    public function testStaticFrom(): void
    {
        $xs = [1, 2, 3];
        $this->assertEquals($xs, Enumerable::from($xs)->toArray());
    }

    public function testStaticCatch(): void
    {
        $xs = Enumerable::defer(function(): iterable {
            yield 1;
            yield 2;
            yield 3;
            throw new \Exception();
        });
        $ys = [4, 5, 6];
        $zs = [7, 8, 9];
        $this->assertEquals([1, 2, 3, 4, 5, 6], Enumerable::catch($xs, $ys, $zs)->toArray());

        $xs = Enumerable::defer(function(): iterable {
            yield 1;
            yield 2;
            yield 3;
            throw new \Exception();
        });
        $this->assertThrows(function() use ($xs) { Enumerable::catch($xs, $xs)->toArray(); });
    }

    public function testStaticConcat(): void
    {
        /** @var mixed[] */
        $xs = [];
        $this->assertEquals([], Enumerable::concat($xs, $xs)->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6], Enumerable::concat([1, 2, 3], [4, 5, 6])->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], Enumerable::concat([1, 2, 3], [4, 5, 6], [7, 8, 9])->toArray());
    }

    public function testStaticDefer(): void
    {
        $this->assertEquals([1, 2, 3], Enumerable::defer(function(): iterable {
            yield 1;
            yield 2;
            yield 3;
        })->toArray());
    }

    public function testStaticGenerate(): void
    {
        $this->assertEquals([0, 1, 4, 9, 16], Enumerable::generate(0, function($x) { return $x < 5; }, function($x) { return $x + 1; }, function($x) { return $x * $x; })->toArray());
    }

    public function testStaticIf(): void
    {
        $this->assertEquals([1, 2, 3], Enumerable::if(function() { return true; }, [1, 2, 3], [4, 5, 6])->toArray());
        $this->assertEquals([4, 5, 6], Enumerable::if(function() { return false; }, [1, 2, 3], [4, 5, 6])->toArray());
    }

    public function testStaticOnErrorResumeNext(): void
    {
        $xs = Enumerable::defer(function(): iterable {
            yield 1;
            yield 2;
            throw new \Exception();
        });
        $ys = Enumerable::defer(function(): iterable {
            yield 3;
            yield 4;
            throw new \Exception();
        });
        $this->assertEquals([1, 2, 3, 4], Enumerable::onErrorResumeNext($xs, $ys)->toArray());
    }

    public function testStaticRange(): void
    {
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], Enumerable::range(1, 10)->toArray());
    }

    public function testStaticRepeat(): void
    {
        $this->assertIterator([123, 123, 123, 123], Enumerable::repeat(123)->toIterator());
        $this->assertEquals([], Enumerable::repeat(123, 0)->toArray());
        $this->assertEquals([123, 123, 123, 123], Enumerable::repeat(123, 4)->toArray());
        $this->assertThrows(function() { Enumerable::repeat(123, -1); });
    }

    public function testStaticReturn(): void
    {
        $this->assertEquals([123], Enumerable::return(123)->toArray());
    }

    public function testStaticZip(): void
    {
        $this->assertEquals([[1, 2], [3, 4], [5, 6], [7, 8]], Enumerable::zip([1, 3, 5, 7], [2, 4, 6, 8], function($x, $y) { return [$x, $y]; })->toArray());
        $this->assertEquals([[1, 2], [3, 4], [5, 6], [7, 8]], Enumerable::zip([1, 3, 5, 7, 9], [2, 4, 6, 8], function($x, $y) { return [$x, $y]; })->toArray());
        $this->assertEquals([[1, 2], [3, 4], [5, 6], [7, 8]], Enumerable::zip([1, 3, 5, 7], [2, 4, 6, 8, 10], function($x, $y) { return [$x, $y]; })->toArray());
        $this->assertEquals([], Enumerable::zip([], [], function($x, $y) { return [$x, $y]; })->toArray());
    }

    public function testStaticEmpty(): void
    {
        $this->assertEquals([], Enumerable::empty()->toArray());
    }

    public function testAggregate(): void
    {
        $this->assertSame(10, Enumerable::from([1, 2, 3, 4])->aggregate(0, function($total, $n) { return $total + $n; }));
        $this->assertSame(2, $this->getEmpty()->aggregate(2, function($total, $n) { return $total + $n; }));
    }

    public function testAll(): void
    {
        $this->assertTrue(Enumerable::from([1, 2, 3, 4])->all());
        $this->assertTrue(Enumerable::from([2, 4])->all(function($n) { return $n % 2 === 0; }));
        $this->assertFalse(Enumerable::from([0, 1, 2, 3])->all(function($n) { return $n % 2 === 0; }));
        $this->assertFalse(Enumerable::from([0, 1, 2, 3])->all());
        $this->assertTrue($this->getEmpty()->all());
        $this->assertTrue($this->getEmpty()->all(function($n) { return $n % 2 === 0; }));
    }

    public function testAny(): void
    {
        $this->assertTrue(Enumerable::from([0, 1, 2, 3, 4])->any());
        $this->assertTrue(Enumerable::from([1, 2, 3, 4])->any(function($n) { return $n % 2 === 0; }));
        $this->assertFalse(Enumerable::from([0])->any());
        $this->assertFalse(Enumerable::from([1, 3])->any(function($n) { return $n % 2 === 0; }));
        $this->assertFalse($this->getEmpty()->any());
        $this->assertFalse($this->getEmpty()->any(function($n) { return $n % 2 === 0; }));
    }

    public function testAverage(): void
    {
        $this->assertSame(2, Enumerable::from([0, 1, 2, 3, 4])->average());
        $this->assertSame(4, Enumerable::from([0, 1, 2, 3, 4])->average(function($n) { return $n * 2; }));

        $this->assertThrows(function() { $this->getEmpty()->average(); });
        $this->assertThrows(function() { $this->getEmpty()->average(function($n) { return $n * 2; }); });
    }

    public function testBuffer(): void
    {
        $xs = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->assertEquals([[0, 1, 2], [3, 4, 5], [6, 7, 8], [9]], Enumerable::from($xs)->buffer(3)->toArray());
        $this->assertEquals([[0, 1, 2, 3, 4], [5, 6, 7, 8, 9]], Enumerable::from($xs)->buffer(5)->toArray());
        $this->assertEquals([], $this->getEmpty()->buffer(5)->toArray());
        $this->assertEquals([[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8], [8, 9]], Enumerable::from($xs)->buffer(3, 2)->toArray());
        $this->assertEquals([[0, 1, 2], [4, 5, 6], [8, 9]], Enumerable::from($xs)->buffer(3, 4)->toArray());

        $this->assertThrows(function() { $this->getEmpty()->buffer(0)->toArray(); }, \OutOfRangeException::class);
        $this->assertThrows(function() { $this->getEmpty()->buffer(1, 0)->toArray(); }, \OutOfRangeException::class);
        $this->assertThrows(function() { $this->getEmpty()->buffer(0, 1)->toArray(); }, \OutOfRangeException::class);
        $this->assertThrows(function() { $this->getEmpty()->buffer(0, 0)->toArray(); }, \OutOfRangeException::class);
    }

    public function testCatch(): void
    {
        /** @var MockObject&callable(mixed):mixed */
        $handler = $this->createMock(Spy::class);
        $handler
            ->expects($this->never())
            ->method('__invoke');
        $this->assertEquals([1, 2, 3], Enumerable::from([1, 2, 3])->catch($handler)->toArray());

        $iteratorFn = function(): iterable {
            yield 1;
            yield 2;
            yield 3;
            throw new \Exception();
        };
        /** @var MockObject&callable(mixed):mixed */
        $handler = $this->createMock(Spy::class);
        $handler
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->isInstanceOf(\Exception::class))
            ->willReturn([4, 5, 6]);
        $this->assertEquals([1, 2, 3, 4, 5, 6], Enumerable::defer($iteratorFn)->catch($handler)->toArray());
    }

    public function testConcat(): void
    {
        $this->assertEquals([], $this->getEmpty()->concat([])->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6], Enumerable::from([1, 2, 3])->concat([4, 5, 6])->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], Enumerable::from([1, 2, 3])->concat([4, 5, 6])->concat([7, 8, 9])->toArray());
    }

    public function testCount(): void
    {
        $this->assertSame(4, Enumerable::from([1, 2, 3, 4])->count());
        $this->assertSame(4, Enumerable::from(new \ArrayIterator([1, 2, 3, 4]))->count());
        $this->assertSame(4, Enumerable::from(new \IteratorIterator(new \ArrayIterator([1, 2, 3, 4])))->count());
        $this->assertSame(0, $this->getEmpty()->count());
        $this->assertSame(2, Enumerable::from([1, 2, 3, 4])->count(function($n) { return $n % 2 === 0; }));
    }

    public function testDefaultIfEmpty(): void
    {
        $this->assertEquals([1, 2, 3], Enumerable::from([1, 2, 3])->defaultIfEmpty(123)->toArray());
        $this->assertEquals([123], $this->getEmpty()->defaultIfEmpty(123)->toArray());
    }

    public function testDistinct(): void
    {
        $this->assertEquals([1, 2], Enumerable::from([1, 2, 1, 2])->distinct()->toArray());
        $this->assertEquals([1, 2], Enumerable::from([1, 1, 2, 2])->distinct()->toArray());
        $this->assertEquals([1, 2], Enumerable::from([1, 2, 3, 4])->distinct(function($x) { return $x % 2 === 0; })->toArray());
        $this->assertEquals([1, 2], Enumerable::from([1, 3, 2, 4])->distinct(function($x) { return $x % 2 === 0; })->toArray());
    }

    public function testDistinctUntilChanged(): void
    {
        $this->assertEquals([1, 2, 1, 2], Enumerable::from([1, 2, 1, 2])->distinctUntilChanged()->toArray());
        $this->assertEquals([1, 2], Enumerable::from([1, 1, 2, 2])->distinctUntilChanged()->toArray());
        $this->assertEquals([1, 2, 3, 4], Enumerable::from([1, 2, 3, 4])->distinctUntilChanged(function($x) { return $x % 2 === 0; })->toArray());
        $this->assertEquals([1, 2], Enumerable::from([1, 3, 2, 4])->distinctUntilChanged(function($x) { return $x % 2 === 0; })->toArray());
    }

    public function testDo(): void
    {
        /** @var MockObject&callable(mixed):void */
        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->never())
            ->method('__invoke');
        Enumerable::from([1, 2, 3, 4])->do($action);

        /** @var MockObject&callable(mixed):void */
        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->exactly(4))
            ->method('__invoke')
            ->withConsecutive(
                [1],
                [2],
                [3],
                [4]
            );
        $this->assertEquals([1, 2, 3, 4], Enumerable::from([1, 2, 3, 4])->do($action)->toArray());
    }

    public function testDoWhile(): void
    {
        $x = 0;
        $iteratorFn = function() use (&$x): iterable {
                yield $x++;
        };
        $this->assertEquals([0, 1, 2, 3, 4], Enumerable::defer($iteratorFn)->doWhile(function() use (&$x) { return $x < 5; })->toArray());
        $this->assertEquals([5], Enumerable::defer($iteratorFn)->doWhile(function() use(&$x) { return $x < 5; })->toArray());
    }

    public function testElementAt(): void
    {
        $xs = [1, 2, 3, 4];
        $this->assertSame(1, Enumerable::from($xs)->elementAt(0));
        $this->assertSame(2, Enumerable::from($xs)->elementAt(1));
        $this->assertSame(3, Enumerable::from($xs)->elementAt(2));
        $this->assertSame(4, Enumerable::from($xs)->elementAt(3));
        $this->assertSame(1, Enumerable::from(new \ArrayIterator($xs))->elementAt(0));
        $this->assertSame(2, Enumerable::from(new \ArrayIterator($xs))->elementAt(1));
        $this->assertSame(3, Enumerable::from(new \ArrayIterator($xs))->elementAt(2));
        $this->assertSame(4, Enumerable::from(new \ArrayIterator($xs))->elementAt(3));

        $xs = [1, 2, 3, 4];
        $this->assertThrows(function() use ($xs) { Enumerable::from($xs)->elementAt(4); });
    }

    public function testElementAtOrDefault(): void
    {
        $xs = [1, 2, 3, 4];
        $this->assertSame(1, Enumerable::from($xs)->elementAtOrDefault(0));
        $this->assertSame(2, Enumerable::from($xs)->elementAtOrDefault(1));
        $this->assertSame(3, Enumerable::from($xs)->elementAtOrDefault(2));
        $this->assertSame(4, Enumerable::from($xs)->elementAtOrDefault(3));
        $this->assertSame(1, Enumerable::from(new \ArrayIterator($xs))->elementAtOrDefault(0));
        $this->assertSame(2, Enumerable::from(new \ArrayIterator($xs))->elementAtOrDefault(1));
        $this->assertSame(3, Enumerable::from(new \ArrayIterator($xs))->elementAtOrDefault(2));
        $this->assertSame(4, Enumerable::from(new \ArrayIterator($xs))->elementAtOrDefault(3));

        $xs = [1, 2, 3, 4];
        $this->assertNull(Enumerable::from($xs)->elementAtOrDefault(4, null));
        $this->assertNull(Enumerable::from($xs)->elementAtOrDefault(4));
    }

    public function testExcept(): void
    {
        $this->assertEquals([], $this->getEmpty()->except([])->toArray());
        $this->assertEquals([1, 5, 6], Enumerable::from([1, 2, 3, 4, 5, 6])->except([2, 3, 4])->toArray());
        $this->assertEquals([1, 5, 6], Enumerable::from([1, 2, 3, 4, 5, 6, 1, 2, 3, 4, 5, 6])->except([2, 3, 4])->toArray());
        $this->assertEquals([], Enumerable::from([2, 3, 4])->except([1, 2, 3, 4, 5, 6])->toArray());
    }

    public function testFinally(): void
    {
        /** @var MockObject&callable():void */
        $finallyAction = $this->createMock(Spy::class);
        $finallyAction
            ->expects($this->once())
            ->method('__invoke');
        $this->assertEquals([1, 2, 3], Enumerable::from([1, 2, 3])->finally($finallyAction)->toArray());

        $iteratorFn = function(): iterable {
            yield 1;
            yield 2;
            yield 3;
            throw new \Exception();
        };
        /** @var MockObject&callable():void */
        $finallyAction = $this->createMock(Spy::class);
        $finallyAction
            ->expects($this->once())
            ->method('__invoke');
        $this->assertThrows(function() use ($iteratorFn, $finallyAction) { Enumerable::defer($iteratorFn)->finally($finallyAction)->toArray(); });
    }

    public function testFirst(): void
    {
        $xs = [1, 2, 3, 4];
        $this->assertSame(1, Enumerable::from($xs)->first());
        $this->assertSame(2, Enumerable::from($xs)->first(function($x) { return $x % 2 === 0; }));
        $this->assertSame(1, Enumerable::from(new \ArrayIterator($xs))->first());

        $this->assertThrows(function() { $this->getEmpty()->first(); });
        $this->assertThrows(function() { Enumerable::from(new \EmptyIterator())->first(); });
        $this->assertThrows(function() { Enumerable::from([1, 2, 3, 4])->first(function($x) { return $x > 10; }); });
    }

    public function testFirstOrDefault(): void
    {
        $xs = [1, 2, 3, 4];
        $this->assertSame(1, Enumerable::from($xs)->firstOrDefault());
        $this->assertSame(2, Enumerable::from($xs)->firstOrDefault(function($x) { return $x % 2 === 0; }));
        $this->assertSame(1, Enumerable::from(new \ArrayIterator($xs))->firstOrDefault());

        $this->assertNull($this->getEmpty()->firstOrDefault());
        $this->assertSame(123, $this->getEmpty()->firstOrDefault(null, 123));
        $this->assertNull(Enumerable::from(new \EmptyIterator())->firstOrDefault());
        $this->assertSame(123, Enumerable::from(new \EmptyIterator())->firstOrDefault(null, 123));
        $this->assertNull(Enumerable::from([1, 2, 3, 4])->firstOrDefault(function($x) { return $x > 10; }));
        $this->assertSame(123, Enumerable::from([1, 2, 3, 4])->firstOrDefault(function($x) { return $x > 10; }, 123));
    }

    public function testForEach(): void
    {
        /** @var MockObject&callable(mixed):void */
        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->exactly(4))
            ->method('__invoke')
            ->withConsecutive(
                [1],
                [2],
                [3],
                [4]
            );
        Enumerable::from([1, 2, 3, 4])->forEach($action);
    }

    public function testGroupBy(): void
    {
        $this->assertEquals([['odd', [1, 3]], ['even', [2, 4]]], Enumerable::from([1, 2, 3, 4])->groupBy(function($x) { return $x % 2 === 0 ? 'even' : 'odd'; })->toArray());
        $this->assertEquals([['odd', [2, 6]], ['even', [4, 8]]], Enumerable::from([1, 2, 3, 4])->groupBy(function($x) { return $x % 2 === 0 ? 'even' : 'odd'; }, function($x) { return $x * 2; })->toArray());
        $this->assertEquals([[2, 6], [4, 8]], Enumerable::from([1, 2, 3, 4])->groupBy(function($x) { return $x % 2 === 0 ? 'even' : 'odd'; }, function($x) { return $x * 2; }, function($k, $vs) { return $vs; })->toArray());
    }

    public function testGroupJoin(): void
    {
        $xs = [0, 1, 2];
        $ys = [4, 7, 6, 2, 3, 4, 8, 9];
        $result = Enumerable::from($xs)
            ->groupJoin(
                $ys,
                function($x) { return $x % 3; },
                function($y) { return $y % 3; },
                function($x, $ys) { return $x . '-' . implode('', $ys); }
            )
            ->toArray();
        $this->assertEquals(['0-639', '1-474', '2-28'], $result);

        $xs = [0, 1, 2];
        $ys = [3, 6, 4];
        $result = Enumerable::from($xs)
            ->groupJoin(
                $ys,
                function($x) { return $x % 3; },
                function($y) { return $y % 3; },
                function($x, $ys) { return $x . '-' . implode('', $ys); }
            )
            ->toArray();
        $this->assertEquals(['0-36', '1-4', '2-'], $result);
    }

    public function testIgnoreElements(): void
    {
        /** @var MockObject&iterable<mixed> */
        $iterator = $this->createMock(\IteratorAggregate::class);
        $iterator
            ->expects($this->never())
            ->method('getIterator');
        $this->assertEquals([], Enumerable::from($iterator)->ignoreElements()->toArray());
    }

    public function testIntersect(): void
    {
        $xs = [44, 26, 92, 30, 71, 38];
        $ys = [39, 59, 83, 47, 26, 4, 30];
        $this->assertEquals([26, 30], Enumerable::from($xs)->intersect($ys)->toArray());
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue($this->getEmpty()->isEmpty());
        $this->assertFalse(Enumerable::from([1, 2, 3])->isEmpty());
    }

    public function testJoin(): void
    {
        $xs = [0, 1, 2];
        $ys = [3, 6, 4];
        $result = Enumerable::from($xs)
            ->join(
                $ys,
                function($x) { return $x % 3; },
                function($y) { return $y % 3; },
                function($x, $y) { return $x . '-' . $y; }
            )
            ->toArray();
        $this->assertEquals(['0-3', '0-6', '1-4'], $result);

        $xs = [3, 6, 4];
        $ys = [0, 0, 1, 1, 2, 2];
        $result = Enumerable::from($xs)
            ->join(
                $ys,
                function($x) { return $x % 3; },
                function($y) { return $y % 3; },
                function($x, $y) { return $x . '-' . $y; }
            )
            ->toArray();
        $this->assertEquals(['3-0', '3-0', '6-0', '6-0', '4-1', '4-1'], $result);
    }

    public function testLast(): void
    {
        $xs = [1, 2, 3, 4];
        $this->assertSame(4, Enumerable::from($xs)->lastOrDefault());
        $this->assertSame(4, Enumerable::from(new \ArrayIterator($xs))->lastOrDefault());
        $this->assertSame(3, Enumerable::from($xs)->lastOrDefault(function($x) { return $x % 2 === 1; }));

        $this->assertNull($this->getEmpty()->lastOrDefault());
        $this->assertSame(123, $this->getEmpty()->lastOrDefault(null, 123));
        $this->assertNull(Enumerable::from(new \EmptyIterator())->lastOrDefault());
        $this->assertSame(123, Enumerable::from(new \EmptyIterator())->lastOrDefault(null, 123));
        $this->assertNull(Enumerable::from([1, 2, 3, 4])->lastOrDefault(function($x) { return $x > 10; }));
        $this->assertSame(123, Enumerable::from([1, 2, 3, 4])->lastOrDefault(function($x) { return $x > 10; }, 123));
    }

    public function testLastOrDefault(): void
    {
        $xs = [1, 2, 3, 4];
        $this->assertSame(4, Enumerable::from($xs)->last());
        $this->assertSame(4, Enumerable::from(new \ArrayIterator($xs))->last());
        $this->assertSame(3, Enumerable::from($xs)->last(function($x) { return $x % 2 === 1; }));

        $this->assertThrows(function() { return $this->getEmpty()->last(); });
        $this->assertThrows(function() { return Enumerable::from([1, 2, 3, 4])->last(function($x) { return $x > 10; }); });
    }

    public function testMax(): void
    {
        $this->assertNull($this->getEmpty()->max());
        $this->assertSame(3, Enumerable::from([1, 2, 3, 2, 1])->max());
        $this->assertSame(3, Enumerable::from(['a', 'ab', 'abc', 'ab', 'a'])->max(function($s) { return strlen($s); }));
    }

    public function testMaxBy(): void
    {
        $this->assertEquals([3, 3], Enumerable::from([3, 2, 1, 2, 3])->maxBy(function($x) { return $x; }));
        $this->assertEquals(['abc', 'abc'], Enumerable::from(['ab', 'abc', 'ab', 'a', 'ab', 'abc', 'ab'])->maxBy(function($s) { return strlen($s); }));
    }

    public function testMemoize(): void
    {
        $n = 0;
        $iteratorFn = function() use (&$n): iterable {
            for ($i = 0; $i < 10; $i++) {
                yield $n++;
            }
        };
        $memoized = Enumerable::defer($iteratorFn)->memoize();
        $expected = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->assertEquals(iterator_to_array($memoized), $expected);
        $this->assertEquals(iterator_to_array($memoized), $expected);

        $n = 0;
        $iteratorFn = function() use (&$n): iterable {
            for ($i = 0; $i < 10; $i++) {
                yield $n++;
            }
            throw new \Exception();
        };
        $memoized = Enumerable::defer($iteratorFn)->memoize();
        $expected = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->assertThrows(function() use ($memoized) { return iterator_to_array($memoized); });
        $this->assertEquals($expected, iterator_to_array($memoized));
        $this->assertEquals($expected, iterator_to_array($memoized));
    }

    public function testMin(): void
    {
        $this->assertNull($this->getEmpty()->min());
        $this->assertSame(1, Enumerable::from([1, 2, 3, 2, 1])->min());
        $this->assertSame(1, Enumerable::from(['a', 'ab', 'abc', 'ab', 'a'])->min(function($s) { return strlen($s); }));
    }

    public function testMinBy(): void
    {
        $this->assertEquals([1, 1], Enumerable::from([1, 2, 3, 2, 1])->minBy(function($x) { return $x; }));
        $this->assertEquals(['a', 'a'], Enumerable::from(['ab', 'a', 'ab', 'abc', 'ab', 'a', 'ab'])->minBy(function($s) { return strlen($s); }));
    }

    public function testOnErrorResumeNext(): void
    {
        $xs = function(): iterable {
            yield 1;
            yield 2;
            throw new \Exception();
        };
        $ys = function(): iterable {
            yield 3;
            yield 4;
            throw new \Exception();
        };
        $this->assertEquals(Enumerable::from($xs())->onErrorResumeNext($ys())->toArray(), [1, 2, 3, 4]);
    }

    public function testOuterJoin(): void
    {
        $xs = [0, 1, 2];
        $ys = [3, 6, 4];
        $result = Enumerable::from($xs)
            ->outerJoin(
                $ys,
                function($x) { return $x % 3; },
                function($y) { return $y % 3; },
                function($x, $y) { return $x . '-' . $y; }
            )
            ->toArray();
        $this->assertEquals(['0-3', '0-6', '1-4', '2-'], $result);

        $xs = [3, 6, 4];
        $ys = [0, 0, 1, 1, 2, 2];
        $result = Enumerable::from($xs)
            ->outerJoin(
                $ys,
                function($x) { return $x % 3; },
                function($y) { return $y % 3; },
                function($x, $y) { return $x . '-' . $y; }
            )
            ->toArray();
        $this->assertEquals(['3-0', '3-0', '6-0', '6-0', '4-1', '4-1'], $result);
    }

    public function testOrderBy(): void
    {
        $xs = [3, 2, 4, 1, 1];
        $this->assertEquals([1, 1, 2, 3, 4], Enumerable::from($xs)->orderBy()->toArray());
        $this->assertEquals([1, 1, 2, 3, 4], Enumerable::from($xs)->orderBy()->aggregate([], function($acc, $x) { return array_merge($acc, [$x]); }));
        $this->assertEquals([2, 4, 1, 1, 3], Enumerable::from($xs)->orderBy(function($n) { return $n % 2; })->thenBy(function($n) { return $n; })->toArray());
        $this->assertEquals([2, 4, 1, 1, 3], Enumerable::from($xs)->orderBy(function($n) { return $n % 2; })->thenByDescending(function($n) { return -$n; })->toArray());
    }

    public function testOrderByDescending(): void
    {
        $xs = [3, 2, 4, 1];
        $this->assertEquals([4, 3, 2, 1], Enumerable::from($xs)->orderByDescending()->toArray());
        $this->assertEquals([3, 1, 4, 2], Enumerable::from($xs)->orderByDescending(function($n) { return $n % 2; })->thenByDescending(function($n) { return $n; })->toArray());
    }

    public function testRepeat(): void
    {
        $this->assertIterator([1, 2, 3, 1, 2, 3, 1, 2, 3, 1], Enumerable::from([1, 2, 3])->repeat()->toIterator());
        $this->assertEquals([1, 2, 3, 1, 2, 3], Enumerable::from([1, 2, 3])->repeat(2)->toArray());
    }

    public function testRetry(): void
    {
        $this->assertEquals([1, 2, 3], Enumerable::from([1, 2, 3])->retry()->toArray());
        $this->assertEquals([1, 2, 3], Enumerable::from([1, 2, 3])->retry(2)->toArray());

        $iteratorFn = function(): iterable {
            yield 1;
            yield 2;
            yield 3;
            throw new \Exception();
        };
        $iterator = Enumerable::defer($iteratorFn)->retry(2)->toIterator();
        $this->assertIterator([1, 2, 3, 1, 2, 3], $iterator);
        $this->assertThrows(function() use ($iterator) { $iterator->next(); });
    }

    public function testReverse(): void
    {
        $this->assertEquals([], $this->getEmpty()->reverse()->toArray());
        $this->assertEquals([1, 4, 2, 3], Enumerable::from([3, 2, 4, 1])->reverse()->toArray());
        $this->assertEquals([1, 4, 2, 3], Enumerable::from(new \ArrayIterator([3, 2, 4, 1]))->reverse()->toArray());
    }

    public function testScan(): void
    {
        $this->assertEquals([1, 3, 6, 10, 15], Enumerable::from([1, 2, 3, 4, 5])->scan(0, function($total, $n) { return $total + $n; })->toArray());
        $this->assertEquals([], $this->getEmpty()->scan(0, function($total, $n) { return $total + $n; })->toArray());
    }

    public function testSelect(): void
    {
        $this->assertEquals([2, 4, 6, 8], Enumerable::from([1, 2, 3, 4])->select(function($x) { return $x * 2; })->toArray());
        $this->assertEquals([], $this->getEmpty()->select(function($x) { return $x * 2; })->toArray());
    }

    public function testSelectMany(): void
    {
        $this->assertEquals([1, 2, 2, 4, 3, 6, 4, 8], Enumerable::from([1, 2, 3, 4])->selectMany(function($x): array { return [$x, $x * 2]; })->toArray());
        $this->assertEquals([], $this->getEmpty()->selectMany(function($x): array { return [$x, $x * 2]; })->toArray());
    }

    public function testSingle(): void
    {
        $this->assertSame(1, Enumerable::from([1])->single());
        $this->assertSame(1, Enumerable::from(new \ArrayIterator([1]))->single());
        $this->assertSame(2, Enumerable::from([1, 2])->single(function($x) { return $x % 2 === 0; }));

        $this->assertThrows(function() { $this->getEmpty()->single(); });
        $this->assertThrows(function() { Enumerable::from(new \EmptyIterator())->single(); });
        $this->assertThrows(function() { Enumerable::from([1, 2])->single(); });
        $this->assertThrows(function() { Enumerable::from(new \ArrayIterator([1, 2]))->single(); });
        $this->assertThrows(function() { Enumerable::from([1, 2, 3, 4])->single(function($x) { return $x > 0; }); });
        $this->assertThrows(function() { Enumerable::from([1, 2, 3, 4])->single(function($x) { return $x > 10; }); });
    }

    public function testSingleOrDefault(): void
    {
        $this->assertSame(1, Enumerable::from([1])->singleOrDefault());
        $this->assertSame(1, Enumerable::from(new \ArrayIterator([1]))->singleOrDefault());
        $this->assertSame(2, Enumerable::from([1, 2])->singleOrDefault(function($x) { return $x % 2 === 0; }));

        $this->assertNull($this->getEmpty()->singleOrDefault());
        $this->assertSame(123, $this->getEmpty()->singleOrDefault(null, 123));
        $this->assertNull(Enumerable::from(new \EmptyIterator())->singleOrDefault());
        $this->assertSame(123, Enumerable::from(new \EmptyIterator())->singleOrDefault(null, 123));
        $this->assertNull(Enumerable::from([1, 2])->singleOrDefault());
        $this->assertSame(123, Enumerable::from([1, 2])->singleOrDefault(null, 123));
        $this->assertNull(Enumerable::from(new \ArrayIterator([1, 2]))->singleOrDefault());
        $this->assertSame(123, Enumerable::from(new \ArrayIterator([1, 2]))->singleOrDefault(null, 123));
        $this->assertNull(Enumerable::from([1, 2, 3, 4])->singleOrDefault(function($x) { return $x > 0; }));
        $this->assertSame(123, Enumerable::from([1, 2, 3, 4])->singleOrDefault(function($x) { return $x > 0; }, 123));
        $this->assertNull(Enumerable::from([1, 2, 3, 4])->singleOrDefault(function($x) { return $x > 10; }));
        $this->assertSame(123, Enumerable::from([1, 2, 3, 4])->singleOrDefault(function($x) { return $x > 10; }, 123));
    }

    public function testSkip(): void
    {
        $this->assertEquals([], $this->getEmpty()->skip(1)->toArray());
        $this->assertEquals([0, 1, 2, 3, 4], Enumerable::from([0, 1, 2, 3, 4])->skip(0)->toArray());
        $this->assertEquals([3, 4], Enumerable::from([0, 1, 2, 3, 4])->skip(3)->toArray());
        $this->assertEquals([3, 4], Enumerable::from(new \ArrayIterator([0, 1, 2, 3, 4]))->skip(3)->toArray());
    }

    public function testSkipLast(): void
    {
        $this->assertEquals([], $this->getEmpty()->skipLast(1)->toArray());
        $this->assertEquals([0, 1, 2, 3, 4], Enumerable::from([0, 1, 2, 3, 4])->skipLast(0)->toArray());
        $this->assertEquals([0, 1], Enumerable::from([0, 1, 2, 3, 4])->skipLast(3)->toArray());
    }

    public function testSkipWhile(): void
    {
        $this->assertEquals([1, 2, 3, 4], Enumerable::from([1, 2, 3, 4])->skipWhile(function($x) { return $x % 2 === 0; })->toArray());
        $this->assertEquals([3, 2, 1], Enumerable::from([4, 3, 2, 1])->skipWhile(function($x) { return $x % 2 === 0; })->toArray());
    }

    public function testStartWith(): void
    {
        $this->assertEquals([0, 0, 1, 2, 3, 4], Enumerable::from([0, 1, 2, 3, 4])->startWith(0)->toArray());
    }

    public function testSum(): void
    {
        $this->assertSame(6, Enumerable::from([1, 2, 3])->sum());
        $this->assertSame(12, Enumerable::from([1, 2, 3])->sum(function($x) { return $x * 2; }));
    }

    public function testTake(): void
    {
        $this->assertEquals([1, 2], Enumerable::from([1, 2, 3, 4])->take(2)->toArray());
        $this->assertEquals([], Enumerable::from([1, 2, 3, 4])->take(0)->toArray());
    }

    public function testTakeLast(): void
    {
        $xs = [1, 2, 3, 4, 5];
        $this->assertEquals([], Enumerable::from($xs)->takeLast(0)->toArray());
        $this->assertEquals([1, 2, 3, 4, 5], Enumerable::from($xs)->takeLast(5)->toArray());
        $this->assertEquals([3, 4, 5], Enumerable::from($xs)->takeLast(3)->toArray());
    }

    public function testTakeWhile(): void
    {
        $this->assertEquals([], Enumerable::from([1, 2, 3, 4])->takeWhile(function($x) { return $x % 2 === 0; })->toArray());
        $this->assertEquals([4], Enumerable::from([4, 3, 2, 1])->takeWhile(function($x) { return $x % 2 === 0; })->toArray());
    }

    public function testToArray(): void
    {
        $this->assertEquals([], $this->getEmpty()->toArray());
        $this->assertEquals([1, 2, 3], Enumerable::from([1, 2, 3])->toArray());
        $this->assertEquals([1, 2, 3], Enumerable::from(new \ArrayIterator([1, 2, 3]))->toArray());
    }

    public function testToDictionary(): void
    {
        $this->assertEquals([1 => 'i', 2 => 'gh', 3 => 'def'], Enumerable::from(['a', 'bc', 'def', 'gh', 'i'])->toDictionary(function($x) { return strlen($x); }));
        $this->assertEquals(['a' => 1, 'bc' => 2, 'def' => 3, 'gh' => 2, 'i' => 1], Enumerable::from(['a', 'bc', 'def', 'gh', 'i'])->toDictionary(function($x) { return $x; }, function($x) { return strlen($x); }));
    }

    public function testToLookup(): void
    {
        $this->assertEquals([1 => ['a', 'i'], 2 => ['bc', 'gh'], 3 => ['def']], Enumerable::from(['a', 'bc', 'def', 'gh', 'i'])->toLookup(function($x) { return strlen($x); }));
        $this->assertEquals([1 => ['aa', 'ii'], 2 => ['bcbc', 'ghgh'], 3 => ['defdef']], Enumerable::from(['a', 'bc', 'def', 'gh', 'i'])->toLookup(function($x) { return strlen($x); }, function($x) { return $x . $x; }));
    }

    public function testToIterator(): void
    {
        $this->assertInstanceOf(\Iterator::class, $this->getEmpty()->toIterator());
        $this->assertInstanceOf(\Iterator::class, Enumerable::from($this->getEmpty())->toIterator());
        $this->assertInstanceOf(\Iterator::class, Enumerable::from(new \ArrayIterator([]))->toIterator());
    }

    public function testUnion(): void
    {
        $this->assertEquals([2, 3], Enumerable::from([1, 2, 3])->union([2, 3, 4])->toArray());
        $this->assertEquals([], Enumerable::from([1, 2, 3])->union([])->toArray());
        $this->assertEquals([], $this->getEmpty()->union([2, 3, 4])->toArray());
        $this->assertEquals([], $this->getEmpty()->union([])->toArray());
    }

    public function testWhere(): void
    {
        $this->assertEquals([2, 4], Enumerable::from([1, 2, 3, 4])->where(function($x) { return $x % 2 == 0; })->toArray());
    }

    public function testWhile(): void
    {
        $x = 5;
        $iteratorFn = function() use (&$x): iterable {
            yield $x--;
        };
        $this->assertEquals([5, 4, 3, 2, 1], Enumerable::defer($iteratorFn)->while(function() use (&$x) { return $x > 0; })->toArray());
    }

    public function testZip(): void
    {
        $this->assertEquals([[1, 2], [3, 4], [5, 6], [7, 8]], Enumerable::from([1, 3, 5, 7])->zip([2, 4, 6, 8], function($x, $y) { return [$x, $y]; })->toArray());
        $this->assertEquals([[1, 2], [3, 4], [5, 6], [7, 8]], Enumerable::from([1, 3, 5, 7, 9])->zip([2, 4, 6, 8], function($x, $y) { return [$x, $y]; })->toArray());
        $this->assertEquals([[1, 2], [3, 4], [5, 6], [7, 8]], Enumerable::from([1, 3, 5, 7])->zip([2, 4, 6, 8, 10], function($x, $y) { return [$x, $y]; })->toArray());
        $this->assertEquals([], $this->getEmpty()->zip([], function($x, $y) { return [$x, $y]; })->toArray());
    }

    /**
     * @param class-string $expectedException
     */
    protected function assertThrows(callable $action, string $expectedException = 'Exception'): void
    {
        try {
            $action();
        } catch (PHPUnitException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->assertInstanceOf($expectedException, $e);
            return;
        }
        $this->fail("Failed asserting that the action throws '$expectedException'.");
    }

    /**
     * @template T
     * @param T[] $expectedValues
     * @param \Iterator<T> $iterator
     */
    protected function assertIterator(array $expectedValues, \Iterator $iterator): void
    {
        $firstLoop = true;
        foreach ($expectedValues as $key => $value) {
            if ($firstLoop) {
                $firstLoop = false;
                $iterator->rewind();
            } else {
                $iterator->next();
            }
            $this->assertTrue($iterator->valid());
            $this->assertSame($key, $iterator->key());
            $this->assertSame($value, $iterator->current());
        }
    }

    /**
     * @return EnumerableInterface<mixed>
     */
    private function getEmpty(): EnumerableInterface
    {
        /** @var mixed[] */
        $xs = [];
        return Enumerable::from($xs);
    }
}
