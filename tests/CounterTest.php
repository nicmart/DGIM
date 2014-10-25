<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace NicMart\DGIM\Test;


use NicMart\DGIM\Bucket;
use NicMart\DGIM\BucketSequence;
use NicMart\DGIM\Counter;

class CounterTest extends \PHPUnit_Framework_TestCase
{
    public function testCount()
    {
        $counter = new Counter(7, 3);

        $counter
            ->input(1) // b1(0): [0:1] b2(0):
            ->input(2) // b1(1): [0:1] b2(1): [1:1]
            ->input(0) // (2)
            ->input(1) //b1(3): [3:1] [0:1] b2(3): [1:1]
            ->input(2) //b1(4): [3:1] [0:1] b2(4): [4:1] [1:1]
            ->input(0) //(5)
            ->input(3) //b1(6): [6:1] [3:2] b2(6): [6:1] [4:2]
        ;

        $this->assertSame(3, $counter->getCount(1));
        $this->assertSame(3, $counter->getCount(2));
        $this->assertSame(5, $counter->getCount(3));
        $this->assertSame(6, $counter->getCount(4));
        $this->assertSame(6, $counter->getCount(5));
        $this->assertSame(6, $counter->getCount(6));
        $this->assertSame(6, $counter->getCount(7));
    }
}
 