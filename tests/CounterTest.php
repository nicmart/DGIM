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
    public function testWithMultipleBits()
    {
        $memory = memory_get_usage();
        new BucketSequence(10, 2, 2);
        $bucket = new Bucket(1, 23);
        //$bucket2 = new Bucket(123232, 23);
        //$bucket3 = new Bucket(123232, 23);
        $m2 = memory_get_usage();
        var_dump("memory: " . ($m2 - $memory - 48) . "B");
        return;
        $size = 10000;
        list($ary, $counter) = $this->randomCounterAndAry($size, 1000, 101);

        $errors = array();
        for ($n = 1; $n < $size; $n = max($n+1, (int) ($n * 1.5))) {
            $expected = $counter->getCount($n);
            $real = array_sum(array_slice($ary, -$n));
            $error = $real ? round((abs($real - $expected) / $real) * 100) : 0;
            $errors[] = $error;
           // if ($error < 30) continue;
            echo "N: " . $n . PHP_EOL;
            echo "Predicted: " . $expected . PHP_EOL;
            echo "Real: " . $real . PHP_EOL;
            echo "Error: $error%" . PHP_EOL;
        }
        echo "Average Error: " . array_sum($errors) / count($errors) . PHP_EOL;
        echo "Max Error: " . max($errors);

        $this->assertTrue(true);
    }

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

    private function randomCounterAndAry($windowSize, $maxInt, $maxForSameSize)
    {
        $counter = new Counter($windowSize, $maxInt, $maxForSameSize);
        $ary = [];

        for ($i = 0; $i < $windowSize; $i++) {
            $rand = rand(0, $maxInt);
            $ary[] = $rand;
            $counter->input($rand);
        }

        return [$ary, $counter];
    }
}
 