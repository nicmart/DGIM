<?php
/*
 * This file is part of DGIM.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NicMart\DGIM\Test;

use NicMart\DGIM\Bucket;
use NicMart\DGIM\BucketSequence;

/**
 * Unit tests for class FirstClass
 */
class BucketSequenceTest extends \PHPUnit_Framework_TestCase
{

    public function testInsertAndConsolidation()
    {
        $sequence = new BucketSequence(10);

        $sequence
            ->input(1)
            ->input(1)
            ->input(1)
            ->input(0)
            ->input(0)
        ;

        $this->assertSame("2:1, 1:2", (string) $sequence);

        $sequence->input(1);
        $sequence->input(0);
        $this->assertSame("5:1, 2:1, 1:2", (string) $sequence);

        $sequence->input(0);
        $sequence->input(1);
        $this->assertSame("8:1, 5:2, 1:2", (string) $sequence);

        $sequence->input(0);
        $sequence->input(0);
        $sequence->input(1);
        $this->assertSame("1:1, 8:1, 5:2", (string) $sequence);
    }

    public function testRemoveEarliest()
    {
        $sequence = new BucketSequence(10);

        $sequence
            ->input(1)
            ->input(1)
            ->input(1)
        ;

        $sequence->removeEarliestBucket();
        $sequence->removeEarliestBucket();

        $this->assertTrue($sequence->isEmpty());
    }
}