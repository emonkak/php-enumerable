<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Enumerable;
use Emonkak\Enumerable\EnumerableExtensions;
use Emonkak\Enumerable\Internal\Converters;
use Emonkak\Enumerable\Internal\Errors;
use Emonkak\Enumerable\Iterator\BufferIterator;
use Emonkak\Enumerable\Iterator\CatchIterator;
use Emonkak\Enumerable\Iterator\ConcatIterator;
use Emonkak\Enumerable\Iterator\DefaultIfEmptyIterator;
use Emonkak\Enumerable\Iterator\DeferIterator;
use Emonkak\Enumerable\Iterator\DistinctIterator;
use Emonkak\Enumerable\Iterator\DistinctUntilChangedIterator;
use Emonkak\Enumerable\Iterator\DoIterator;
use Emonkak\Enumerable\Iterator\DoWhileIterator;
use Emonkak\Enumerable\Iterator\EmptyIterator;
use Emonkak\Enumerable\Iterator\ExceptIterator;
use Emonkak\Enumerable\Iterator\FinallyIterator;
use Emonkak\Enumerable\Iterator\GenerateIterator;
use Emonkak\Enumerable\Iterator\GroupByIterator;
use Emonkak\Enumerable\Iterator\GroupJoinIterator;
use Emonkak\Enumerable\Iterator\IfIterator;
use Emonkak\Enumerable\Iterator\IntersectIterator;
use Emonkak\Enumerable\Iterator\JoinIterator;
use Emonkak\Enumerable\Iterator\MemoizeIterator;
use Emonkak\Enumerable\Iterator\OnErrorResumeNextIterator;
use Emonkak\Enumerable\Iterator\OrderByIterator;
use Emonkak\Enumerable\Iterator\OuterJoinIterator;
use Emonkak\Enumerable\Iterator\RangeIterator;
use Emonkak\Enumerable\Iterator\RepeatIterator;
use Emonkak\Enumerable\Iterator\RetryIterator;
use Emonkak\Enumerable\Iterator\ReverseIterator;
use Emonkak\Enumerable\Iterator\ScanIterator;
use Emonkak\Enumerable\Iterator\SelectIterator;
use Emonkak\Enumerable\Iterator\SelectManyIterator;
use Emonkak\Enumerable\Iterator\SkipIterator;
use Emonkak\Enumerable\Iterator\SkipLastIterator;
use Emonkak\Enumerable\Iterator\SkipWhileIterator;
use Emonkak\Enumerable\Iterator\StartWithIterator;
use Emonkak\Enumerable\Iterator\StaticCatchIterator;
use Emonkak\Enumerable\Iterator\StaticRepeatIterator;
use Emonkak\Enumerable\Iterator\TakeIterator;
use Emonkak\Enumerable\Iterator\TakeLastIterator;
use Emonkak\Enumerable\Iterator\TakeWhileIterator;
use Emonkak\Enumerable\Iterator\UnionIterator;
use Emonkak\Enumerable\Iterator\WhereIterator;
use Emonkak\Enumerable\Iterator\WhileIterator;
use Emonkak\Enumerable\Iterator\ZipIterator;
use PHPUnit\Exception as PHPUnitException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BufferIterator::class)]
#[CoversClass(CatchIterator::class)]
#[CoversClass(ConcatIterator::class)]
#[CoversClass(Converters::class)]
#[CoversClass(DefaultIfEmptyIterator::class)]
#[CoversClass(DeferIterator::class)]
#[CoversClass(DistinctIterator::class)]
#[CoversClass(DistinctUntilChangedIterator::class)]
#[CoversClass(DoIterator::class)]
#[CoversClass(DoWhileIterator::class)]
#[CoversClass(EmptyIterator::class)]
#[CoversClass(Enumerable::class)]
#[CoversClass(EnumerableExtensions::class)]
#[CoversClass(Errors::class)]
#[CoversClass(ExceptIterator::class)]
#[CoversClass(FinallyIterator::class)]
#[CoversClass(GenerateIterator::class)]
#[CoversClass(GroupByIterator::class)]
#[CoversClass(GroupJoinIterator::class)]
#[CoversClass(IfIterator::class)]
#[CoversClass(IntersectIterator::class)]
#[CoversClass(JoinIterator::class)]
#[CoversClass(MemoizeIterator::class)]
#[CoversClass(OnErrorResumeNextIterator::class)]
#[CoversClass(OrderByIterator::class)]
#[CoversClass(OuterJoinIterator::class)]
#[CoversClass(RangeIterator::class)]
#[CoversClass(RepeatIterator::class)]
#[CoversClass(RetryIterator::class)]
#[CoversClass(ReverseIterator::class)]
#[CoversClass(ScanIterator::class)]
#[CoversClass(SelectIterator::class)]
#[CoversClass(SelectManyIterator::class)]
#[CoversClass(SkipIterator::class)]
#[CoversClass(SkipLastIterator::class)]
#[CoversClass(SkipWhileIterator::class)]
#[CoversClass(StartWithIterator::class)]
#[CoversClass(StaticCatchIterator::class)]
#[CoversClass(StaticRepeatIterator::class)]
#[CoversClass(TakeIterator::class)]
#[CoversClass(TakeLastIterator::class)]
#[CoversClass(TakeWhileIterator::class)]
#[CoversClass(UnionIterator::class)]
#[CoversClass(WhereIterator::class)]
#[CoversClass(WhileIterator::class)]
#[CoversClass(ZipIterator::class)]
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
        $this->assertThrows(function() use ($xs) { Enumerable::catch($xs, $xs)->toArray(); });
    }

    public function testStaticConcat(): void
    {
        $this->assertEquals([], Enumerable::concat([], [])->toArray());
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
        $this->assertEquals(
            [0, 1, 4, 9, 16],
            Enumerable::generate(
                0,
                function(int $x): bool { return $x < 5; },
                function(int $x): int { return $x + 1; },
                function(int $x): int { return $x * $x; }
            )->toArray()
        );
    }

    public function testStaticIf(): void
    {
        $this->assertEquals([1, 2, 3], Enumerable::if(function(): bool { return true; }, [1, 2, 3], [4, 5, 6])->toArray());
        $this->assertEquals([4, 5, 6], Enumerable::if(function(): bool { return false; }, [1, 2, 3], [4, 5, 6])->toArray());
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
        $aggregateFn = function(int $total, int $n): int { return $total + $n; };

        $this->assertSame(10, Enumerable::from([1, 2, 3, 4])->aggregate(0, $aggregateFn));
        $this->assertSame(2, Enumerable::from([])->aggregate(2, $aggregateFn));
    }

    public function testAll(): void
    {
        $predicateFn = function(int $n): bool { return $n % 2 === 0; };

        $this->assertTrue(Enumerable::from([1, 2, 3, 4])->all());
        $this->assertTrue(Enumerable::from([2, 4])->all($predicateFn));
        $this->assertFalse(Enumerable::from([0, 1, 2, 3])->all($predicateFn));
        $this->assertFalse(Enumerable::from([0, 1, 2, 3])->all());
        $this->assertTrue(Enumerable::from([])->all());
        $this->assertTrue(Enumerable::from([])->all($predicateFn));
    }

    public function testAny(): void
    {
        $predicateFn = function(int $n): bool { return $n % 2 === 0; };

        $this->assertTrue(Enumerable::from([0, 1, 2, 3, 4])->any());
        $this->assertTrue(Enumerable::from([1, 2, 3, 4])->any($predicateFn));
        $this->assertFalse(Enumerable::from([0])->any());
        $this->assertFalse(Enumerable::from([1, 3])->any($predicateFn));
        $this->assertFalse(Enumerable::from([])->any());
        $this->assertFalse(Enumerable::from([])->any($predicateFn));
    }

    public function testAverage(): void
    {
        $selectorFn = function(int $n): int { return $n * 2; };

        $this->assertSame(2, Enumerable::from([0, 1, 2, 3, 4])->average());
        $this->assertSame(4, Enumerable::from([0, 1, 2, 3, 4])->average($selectorFn));

        $this->assertThrows(function(): void { Enumerable::from([])->average(); });
        $this->assertThrows(function() use ($selectorFn): void { Enumerable::from([])->average($selectorFn); });
    }

    public function testBuffer(): void
    {
        $xs = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->assertEquals([[0, 1, 2], [3, 4, 5], [6, 7, 8], [9]], Enumerable::from($xs)->buffer(3)->toArray());
        $this->assertEquals([[0, 1, 2, 3, 4], [5, 6, 7, 8, 9]], Enumerable::from($xs)->buffer(5)->toArray());
        $this->assertEquals([], Enumerable::from([])->buffer(5)->toArray());
        $this->assertEquals([[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8], [8, 9]], Enumerable::from($xs)->buffer(3, 2)->toArray());
        $this->assertEquals([[0, 1, 2], [4, 5, 6], [8, 9]], Enumerable::from($xs)->buffer(3, 4)->toArray());
        $this->assertEquals([[0, 1, 2], [5, 6, 7]], Enumerable::from($xs)->buffer(3, 5)->toArray());

        $this->assertThrows(function() { Enumerable::from([])->buffer(0)->toArray(); }, \OutOfRangeException::class);
        $this->assertThrows(function() { Enumerable::from([])->buffer(1, 0)->toArray(); }, \OutOfRangeException::class);
        $this->assertThrows(function() { Enumerable::from([])->buffer(0, 1)->toArray(); }, \OutOfRangeException::class);
        $this->assertThrows(function() { Enumerable::from([])->buffer(0, 0)->toArray(); }, \OutOfRangeException::class);
    }

    public function testCatch(): void
    {
        /** @var int[] */
        $xs = [1, 2, 3];
        /** @var int[] */
        $ys = [4, 5, 6];

        $handler = $this->createMock(Spy::class);
        $handler
            ->expects($this->never())
            ->method('__invoke');

        /** @var callable(\Exception):iterable<int> $handler */
        $this->assertEquals($xs, Enumerable::from($xs)->catch($handler)->toArray());

        /** @var callable():iterable<int> */
        $iteratorFn = function(): iterable {
            yield 1;
            yield 2;
            yield 3;
            throw new \Exception();
        };

        $handler = $this->createMock(Spy::class);
        $handler
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->isInstanceOf(\Exception::class))
            ->willReturn($ys);

        /** @var callable(\Exception):iterable<int> $handler */
        $this->assertEquals([1, 2, 3, 4, 5, 6], Enumerable::defer($iteratorFn)->catch($handler)->toArray());
    }

    public function testConcat(): void
    {
        /** @var int[] */
        $xs = [1, 2, 3];
        /** @var int[] */
        $ys = [4, 5, 6];
        /** @var int[] */
        $zs = [7, 8, 9];

        $this->assertEquals([], Enumerable::from([])->concat([])->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6], Enumerable::from($xs)->concat($ys)->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], Enumerable::from($xs)->concat($ys)->concat($zs)->toArray());
    }

    public function testCount(): void
    {
        $xs = [1, 2, 3, 4];

        $this->assertSame(4, Enumerable::from($xs)->count());
        $this->assertSame(4, Enumerable::from(new \ArrayIterator($xs))->count());
        $this->assertSame(4, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->count());
        $this->assertSame(0, Enumerable::from([])->count());
        $this->assertSame(2, Enumerable::from($xs)->count(function($n) { return $n % 2 === 0; }));
    }

    public function testDefaultIfEmpty(): void
    {
        /** @var int[] */
        $xs = [1, 2, 3];
        /** @var int[] */
        $ys = [];

        $this->assertEquals([1, 2, 3], Enumerable::from($xs)->defaultIfEmpty(123)->toArray());
        $this->assertEquals([123], Enumerable::from($ys)->defaultIfEmpty(123)->toArray());
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
        $xs = [1, 2, 3, 4];

        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->never())
            ->method('__invoke');

        /** @var callable(int):void $action */
        Enumerable::from($xs)->do($action);

        $expectedArguments = [[1], [2], [3], [4]];

        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->exactly(4))
            ->method('__invoke')
            ->willReturnCallback(function(mixed ...$args) use (&$expectedArguments) {
                $this->assertSame(array_shift($expectedArguments), $args);
            });

        /** @var callable(int):void $action */
        $this->assertEquals($xs, Enumerable::from($xs)->do($action)->toArray());
    }

    public function testDoWhile(): void
    {
        $x = 0;
        $iteratorFn = function() use (&$x): iterable {
            yield $x++;
        };
        $this->assertEquals([0, 1, 2, 3, 4], Enumerable::defer($iteratorFn)->doWhile(function() use (&$x) { return $x < 5; })->toArray());
        $this->assertEquals([5], Enumerable::defer($iteratorFn)->doWhile(function() use (&$x) { return $x < 5; })->toArray());
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
        $this->assertSame(1, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAt(0));
        $this->assertSame(2, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAt(1));
        $this->assertSame(3, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAt(2));
        $this->assertSame(4, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAt(3));
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
        $this->assertSame(1, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAtOrDefault(0));
        $this->assertSame(2, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAtOrDefault(1));
        $this->assertSame(3, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAtOrDefault(2));
        $this->assertSame(4, Enumerable::from(new \IteratorIterator(new \ArrayIterator($xs)))->elementAtOrDefault(3));
        $this->assertNull(Enumerable::from($xs)->elementAtOrDefault(4, null));
        $this->assertNull(Enumerable::from($xs)->elementAtOrDefault(4));
    }

    public function testExcept(): void
    {
        /** @var int[] */
        $xs = [1, 2, 3, 4, 5, 6];
        /** @var int[] */
        $ys = [1, 2, 3, 4, 5, 6, 1, 2, 3, 4, 5, 6];
        /** @var int[] */
        $zs = [2, 3, 4];

        $this->assertEquals([], Enumerable::from([])->except([])->toArray());
        $this->assertEquals([1, 5, 6], Enumerable::from($xs)->except($zs)->toArray());
        $this->assertEquals([1, 5, 6], Enumerable::from($ys)->except($zs)->toArray());
        $this->assertEquals([], Enumerable::from($zs)->except($xs)->toArray());
    }

    public function testFinally(): void
    {
        $xs = [1, 2, 3];

        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->once())
            ->method('__invoke');

        /** @var callable():void $action */
        $this->assertEquals($xs, Enumerable::from($xs)->finally($action)->toArray());

        /** @var callable():iterable<int> */
        $iteratorFn = function(): iterable {
            yield 1;
            yield 2;
            yield 3;
            throw new \Exception();
        };

        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->once())
            ->method('__invoke');

        /** @var callable():void $action */
        $this->assertThrows(function() use ($iteratorFn, $action) { Enumerable::defer($iteratorFn)->finally($action)->toArray(); });
    }

    public function testFirst(): void
    {
        $xs = [1, 2, 3, 4];

        $this->assertSame(1, Enumerable::from($xs)->first());
        $this->assertSame(2, Enumerable::from($xs)->first(function($x) { return $x % 2 === 0; }));
        $this->assertSame(1, Enumerable::from(new \ArrayIterator($xs))->first());

        $this->assertThrows(function() { Enumerable::from([])->first(); });
        $this->assertThrows(function() { Enumerable::from(new \EmptyIterator())->first(); });
        $this->assertThrows(function() { Enumerable::from([1, 2, 3, 4])->first(function($x) { return $x > 10; }); });
    }

    public function testFirstOrDefault(): void
    {
        $xs = [1, 2, 3, 4];

        $this->assertSame(1, Enumerable::from($xs)->firstOrDefault());
        $this->assertSame(2, Enumerable::from($xs)->firstOrDefault(function($x) { return $x % 2 === 0; }));
        $this->assertSame(1, Enumerable::from(new \ArrayIterator($xs))->firstOrDefault());

        $this->assertNull(Enumerable::from([])->firstOrDefault());
        $this->assertSame(123, Enumerable::from([])->firstOrDefault(null, 123));
        $this->assertNull(Enumerable::from(new \EmptyIterator())->firstOrDefault());
        $this->assertSame(123, Enumerable::from(new \EmptyIterator())->firstOrDefault(null, 123));
        $this->assertNull(Enumerable::from([1, 2, 3, 4])->firstOrDefault(function($x) { return $x > 10; }));
        $this->assertSame(123, Enumerable::from([1, 2, 3, 4])->firstOrDefault(function($x) { return $x > 10; }, 123));
    }

    public function testForEach(): void
    {
        $xs = [1, 2, 3, 4];
        $expectedArguments = [[1], [2], [3], [4]];

        $action = $this->createMock(Spy::class);
        $action
            ->expects($this->exactly(4))
            ->method('__invoke')
            ->willReturnCallback(function(mixed ...$args) use (&$expectedArguments) {
                $this->assertSame(array_shift($expectedArguments), $args);
            });

        /** @var callable(int):void $action */
        Enumerable::from($xs)->forEach($action);
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
        $iterator = $this->createMock(\IteratorAggregate::class);
        $iterator
            ->expects($this->never())
            ->method('getIterator');
        $this->assertEquals([], Enumerable::from($iterator)->ignoreElements()->toArray());
    }

    public function testIntersect(): void
    {
        /** @var int[] */
        $xs = [44, 26, 92, 30, 71, 38];
        /** @var int[] */
        $ys = [39, 59, 83, 47, 26, 4, 30];

        $this->assertEquals([26, 30], Enumerable::from($xs)->intersect($ys)->toArray());
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(Enumerable::from([])->isEmpty());
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

        $this->assertNull(Enumerable::from([])->lastOrDefault());
        $this->assertSame(123, Enumerable::from([])->lastOrDefault(null, 123));
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
        $this->assertThrows(function(): void { Enumerable::from([])->last(); });
        $this->assertThrows(function(): void { Enumerable::from([1, 2, 3, 4])->last(function($x) { return $x > 10; }); });
    }

    public function testMax(): void
    {
        $this->assertNull(Enumerable::from([])->max());
        $this->assertSame(3, Enumerable::from([1, 2, 3, 2, 1])->max());
        $this->assertSame(3, Enumerable::from(['a', 'ab', 'abc', 'ab', 'a'])->max(function($s) { return strlen($s); }));
    }

    public function testMaxBy(): void
    {
        $this->assertEquals([3, 3], Enumerable::from([3, 2, 1, 2, 3])->maxBy(function($x) { return $x; })->toArray());
        $this->assertEquals(['abc', 'abc'], Enumerable::from(['ab', 'abc', 'ab', 'a', 'ab', 'abc', 'ab'])->maxBy(function($s) { return strlen($s); })->toArray());
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
        $this->assertNull(Enumerable::from([])->min());
        $this->assertSame(1, Enumerable::from([1, 2, 3, 2, 1])->min());
        $this->assertSame(1, Enumerable::from(['a', 'ab', 'abc', 'ab', 'a'])->min(function($s) { return strlen($s); }));
    }

    public function testMinBy(): void
    {
        $this->assertEquals([1, 1], Enumerable::from([1, 2, 3, 2, 1])->minBy(function($x) { return $x; })->toArray());
        $this->assertEquals(['a', 'a'], Enumerable::from(['ab', 'a', 'ab', 'abc', 'ab', 'a', 'ab'])->minBy(function($s) { return strlen($s); })->toArray());
    }

    public function testOnErrorResumeNext(): void
    {
        /** @var callable():iterable<int> */
        $xs = function(): iterable {
            yield 1;
            yield 2;
            throw new \Exception();
        };
        /** @var callable():iterable<int> */
        $ys = function(): iterable {
            yield 3;
            yield 4;
            throw new \Exception();
        };
        $this->assertEquals([1, 2, 3, 4], Enumerable::from($xs())->onErrorResumeNext($ys())->toArray());
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
        $aggregateFn = function(array $acc, mixed $x): array { return array_merge($acc, [$x]); };

        $xs = [3, 2, 4, 1, 1];
        $this->assertEquals([1, 1, 2, 3, 4], Enumerable::from($xs)->orderBy()->toArray());
        $this->assertEquals([1, 1, 2, 3, 4], Enumerable::from($xs)->orderBy()->aggregate([], $aggregateFn));
        $this->assertEquals([2, 4, 1, 1, 3], Enumerable::from($xs)->orderBy(function(int $n): int { return $n % 2; })->thenBy(function($n) { return $n; })->toArray());
        $this->assertEquals([2, 4, 1, 1, 3], Enumerable::from($xs)->orderBy(function(int $n): int { return $n % 2; })->thenByDescending(function($n) { return -$n; })->toArray());
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
        $this->assertEquals([], Enumerable::from([])->reverse()->toArray());
        $this->assertEquals([1, 4, 2, 3], Enumerable::from([3, 2, 4, 1])->reverse()->toArray());
        $this->assertEquals([1, 4, 2, 3], Enumerable::from(new \ArrayIterator([3, 2, 4, 1]))->reverse()->toArray());
    }

    public function testScan(): void
    {
        $scanFn = function(int $total, int $n): int { return $total + $n; };

        $this->assertEquals([1, 3, 6, 10, 15], Enumerable::from([1, 2, 3, 4, 5])->scan(0, $scanFn)->toArray());
        $this->assertEquals([], Enumerable::from([])->scan(0, $scanFn)->toArray());
    }

    public function testSelect(): void
    {
        $selectorFn1 = function(int $x): int { return $x * 2; };
        $selectorFn2 = function(int $x, int $i): int { return $i; };

        $this->assertEquals([2, 4, 6, 8], Enumerable::from([1, 2, 3, 4])->select($selectorFn1)->toArray());
        $this->assertEquals([0, 1, 2, 3], Enumerable::from([1, 2, 3, 4])->select($selectorFn2)->toArray());
        $this->assertEquals([], Enumerable::from([])->select($selectorFn1)->toArray());
        $this->assertEquals([], Enumerable::from([])->select($selectorFn2)->toArray());
    }

    public function testSelectMany(): void
    {
        $this->assertEquals([1, 2, 2, 4, 3, 6, 4, 8], Enumerable::from([1, 2, 3, 4])->selectMany(function($x): array { return [$x, $x * 2]; })->toArray());
        $this->assertEquals([1, 0, 2, 1, 3, 2, 4, 3], Enumerable::from([1, 2, 3, 4])->selectMany(function($x, $i): array { return [$x, $i]; })->toArray());
        $this->assertEquals([], Enumerable::from([])->selectMany(function($x): array { return [$x, $x * 2]; })->toArray());
    }

    public function testSingle(): void
    {
        $this->assertSame(1, Enumerable::from([1])->single());
        $this->assertSame(1, Enumerable::from(new \ArrayIterator([1]))->single());
        $this->assertSame(2, Enumerable::from([1, 2])->single(function($x) { return $x % 2 === 0; }));

        $this->assertThrows(function() { Enumerable::from([])->single(); });
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

        $this->assertNull(Enumerable::from([])->singleOrDefault());
        $this->assertSame(123, Enumerable::from([])->singleOrDefault(null, 123));
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
        $this->assertEquals([], Enumerable::from([])->skip(1)->toArray());
        $this->assertEquals([0, 1, 2, 3, 4], Enumerable::from([0, 1, 2, 3, 4])->skip(0)->toArray());
        $this->assertEquals([3, 4], Enumerable::from([0, 1, 2, 3, 4])->skip(3)->toArray());
        $this->assertEquals([3, 4], Enumerable::from(new \ArrayIterator([0, 1, 2, 3, 4]))->skip(3)->toArray());
    }

    public function testSkipLast(): void
    {
        $this->assertEquals([], Enumerable::from([])->skipLast(1)->toArray());
        $this->assertEquals([0, 1, 2, 3, 4], Enumerable::from([0, 1, 2, 3, 4])->skipLast(0)->toArray());
        $this->assertEquals([0, 1], Enumerable::from([0, 1, 2, 3, 4])->skipLast(3)->toArray());
    }

    public function testSkipWhile(): void
    {
        $this->assertEquals([1, 2, 3, 4], Enumerable::from([1, 2, 3, 4])->skipWhile(function($x) { return $x % 2 === 0; })->toArray());
        $this->assertEquals([3, 2, 1], Enumerable::from([4, 3, 2, 1])->skipWhile(function($x) { return $x % 2 === 0; })->toArray());
        $this->assertEquals([2, 1], Enumerable::from([4, 3, 2, 1])->skipWhile(function($x, $i) { return $i < 2; })->toArray());
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
        $this->assertEquals([4, 3], Enumerable::from([4, 3, 2, 1])->takeWhile(function($x, $i) { return $i < 2; })->toArray());
    }

    public function testToArray(): void
    {
        $this->assertEquals([], Enumerable::from([])->toArray());
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
        $this->assertInstanceOf(\Iterator::class, Enumerable::from([])->toIterator());
        $this->assertInstanceOf(\Iterator::class, Enumerable::from(Enumerable::from([]))->toIterator());
        $this->assertInstanceOf(\Iterator::class, Enumerable::from(new \ArrayIterator([]))->toIterator());
    }

    public function testUnion(): void
    {
        /** @var int[] */
        $xs = [1, 2, 3];
        /** @var int[] */
        $ys = [2, 3, 4];
        /** @var int[] */
        $zs = [];

        $this->assertEquals([2, 3], Enumerable::from($xs)->union($ys)->toArray());
        $this->assertEquals([], Enumerable::from($xs)->union($zs)->toArray());
        $this->assertEquals([], Enumerable::from($zs)->union($ys)->toArray());
        $this->assertEquals([], Enumerable::from($zs)->union($zs)->toArray());
    }

    public function testWhere(): void
    {
        $this->assertEquals([2, 4], Enumerable::from([1, 2, 3, 4])->where(function(int $x): bool { return $x % 2 == 0; })->toArray());
        $this->assertEquals([1, 3], Enumerable::from([1, 2, 3, 4])->where(function(int $x, int $i): bool { return $i % 2 == 0; })->toArray());
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
        $this->assertEquals([], Enumerable::from([])->zip([], function($x, $y) { return [$x, $y]; })->toArray());
    }

    /**
     * @param callable():void $action
     * @param class-string<\Throwable> $expectedException
     */
    protected function assertThrows(callable $action, string $expectedException = \Exception::class): void
    {
        try {
            $action();
        } catch (PHPUnitException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->assertInstanceOf($expectedException, $e);
            return;
        }
        $this->fail('Failed asserting that exception of type ' . $expectedException . ' is thrown.');
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
}
