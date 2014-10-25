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

class BucketTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAndSetNext()
    {
        $bucket = new Bucket(1, 2);
        $bucket2 = new Bucket(2, 3);
        $bucket3 = new Bucket(3, 4);

        $bucket->setNext($bucket2);
        $bucket2->setNext($bucket3);
        $this->assertSame($bucket2, $bucket->getNext());
        $this->assertSame($bucket3, $bucket->getNext(2));
    }

    public function testGetPrevWithNoPrev()
    {

    }

    public function testGetAndSetPrev()
    {
        $bucket = new Bucket(1, 2);
        $bucket2 = new Bucket(2, 3);
        $bucket3 = new Bucket(3, 4);

        $bucket->setPrev($bucket2);
        $bucket2->setPrev($bucket3);
        $this->assertSame($bucket2, $bucket->getPrev());
        $this->assertSame($bucket3, $bucket->getPrev(2));
    }
}
 