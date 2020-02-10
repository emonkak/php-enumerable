<?php

declare(strict_types=1);

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Sequence;
use PHPUnit\Framework\TestCase;

/**
 * @covers Emonkak\Enumerable\Sequence
 */
class SequenceTest extends TestCase
{
    public function testGetIterator(): void
    {
        $this->assertEquals([1, 2, 3], iterator_to_array(new Sequence([1, 2, 3])));
    }

    public function testGetSource(): void
    {
        $this->assertEquals([1, 2, 3], (new Sequence([1, 2, 3]))->getSource());
    }
}
