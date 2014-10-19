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

    public function testCount()
    {
        $sequence = new BucketSequence(10);

        // 3:1 1:2
        $sequence
            ->input(1)
            ->input(1)
            ->input(0)
            ->input(1)
            ->input(0)
            ->input(0)  //timestamp 5
        ;

        $this->assertSame(0, $sequence->getCount(1));
        $this->assertSame(0, $sequence->getCount(2));
        $this->assertSame(1, $sequence->getCount(3));
        $this->assertSame(1, $sequence->getCount(4));
        $this->assertSame(2, $sequence->getCount(5));

                // 1:1 9:2 7:2
        $sequence
            ->input(0)  //6
            ->input(1)  //7
            ->input(1)  //8
            ->input(1)  //9
            ->input(0)  //0
            ->input(1)  //1
        ;

        $this->assertSame(1, $sequence->getCount(1));
        $this->assertSame(1, $sequence->getCount(2));
        $this->assertSame(2, $sequence->getCount(3));
        $this->assertSame(2, $sequence->getCount(4));
        $this->assertSame(4, $sequence->getCount(5));
        $this->assertSame(4, $sequence->getCount(6));
        $this->assertSame(4, $sequence->getCount(7));
        $this->assertSame(4, $sequence->getCount(8));
        $this->assertSame(4, $sequence->getCount(9));
    }

    public function testRandomSequence()
    {
        $a = $this->randomAry(10000, 100, 33);

        //$a = array_fill(0, 500, 0);
        //$a[499 - 15] = 1;

        $sequence = new BucketSequence(10000);

        foreach ($a as $v) {
            $sequence->input($v);
        }

        echo (string) $sequence;

        $errors = array();
        for ($n = 1; $n < 1000; $n += 1) {
            $expected = $sequence->getCount($n);
            $real = $this->numOfOnes($a, $n);
            $error = $real ? round((abs($real - $expected) / $real) * 100) : 0;
            $errors[] = $error;
            if ($error < 30) continue;
            echo "N: " . $n . PHP_EOL;
            echo "Predicted: " . $expected . PHP_EOL;
            echo "Real: " . $real . PHP_EOL;
            echo "Error: $error%" . PHP_EOL;
        }
        echo "Average Error: " . array_sum($errors) / count($errors);

        $this->assertTrue(true);
    }

    private function randomAry($n, $randomSize, $threshold)
    {
        $a = array();

        for ($i = 0; $i < $n; $i++) {
            $a[$i] = rand(1, $randomSize) <= $threshold;
        }

        return $a;
    }

    private function numOfOnes($a, $k)
    {
        $count = 0;
        $size = count($a);

        for ($i = $size - 1; $i > $size - $k; $i--) {
            if ($a[$i]) {
                $count++;
            }
        }

        return $count;
    }
}