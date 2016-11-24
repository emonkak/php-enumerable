<?php

namespace Emonkak\Enumerable\Tests;

use Emonkak\Enumerable\Sequence;

/**
 * @covers Emonkak\Enumerable\Sequence
 */
class SequenceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $this->assertEquals([1, 2, 3], iterator_to_array(new Sequence([1, 2, 3])));
    }

    public function testGetSource()
    {
        $this->assertEquals([1, 2, 3], (new Sequence([1, 2, 3]))->getSource());
    }
}
