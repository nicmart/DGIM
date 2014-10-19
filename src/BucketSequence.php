<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace NicMart\DGIM;

class BucketSequence
{
    /**
     * @var Bucket
     */
    private $earliestBucket;

    /**
     * @var  Bucket
     */
    private $lastBucket;

    /**
     * @var int
     */
    private $base;

    /**
     * @var int
     */
    private $windowSize;

    /**
     * The timestamp of the next input.
     * It will be stored modulo windowSize
     * @var int
     */
    private $timestamp = 0;

    /**
     * @param int $windowSize
     * @param int $base
     */
    public function __construct($windowSize, $base = 2)
    {
        $this->windowSize = $windowSize;
        $this->base = $base;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !isset($this->earliestBucket);
    }

    /**
     * @return Bucket
     */
    public function getEarliestBucket()
    {
        return $this->earliestBucket;
    }

    /**
     * @return Bucket
     */
    public function getLastBucket()
    {
        return $this->lastBucket;
    }

    /**
     * @return $this
     */
    public function removeEarliestBucket()
    {
        $earliest = $this->getEarliestBucket();

        if (!$earliest->getPrev()) {
            $this->earliestBucket = $this->lastBucket = null;
            return $this;
        }

        $this->earliestBucket = $earliest->getPrev();
        $this->earliestBucket->setNext(null);

        return $this;
    }

    /**
     * @param $bit
     * @return $this
     */
    public function input($bit)
    {
        if ($this->isEarliestBucketStale()) {
            $this->removeEarliestBucket();
        }

        if ($bit) {
            $this->pushBucket(new Bucket($this->timestamp, 0));
            $this->condensateBuckets();
        }

        $this->timestamp = ($this->timestamp + 1) % $this->windowSize;

        return $this;
    }

    /**
     * @param Bucket $bucket
     * @return $this
     */
    private function pushBucket(Bucket $bucket)
    {
        if ($this->isEmpty()) {
            $this->earliestBucket = $this->lastBucket = $bucket;
            return $this;
        }

        $bucket->setNext($this->lastBucket);
        $this->lastBucket->setPrev($bucket);
        $this->lastBucket = $bucket;

        return $this;
    }

    /**
     * Condensate a sequence of $this->base buckets
     * of the same exponent into a bucket of exponent increased by one
     *
     * @return $this
     */
    private function condensateBuckets()
    {
        $sameExpCount = 1;
        $currentBucket = $this->getLastBucket();
        $latestBucketForCurrExp = $currentBucket;

        while ($currentBucket = $currentBucket->getNext()) {
            if ($currentBucket->getExponent() !== $latestBucketForCurrExp->getExponent()) {
                $latestBucketForCurrExp = $currentBucket;
                $sameExpCount = 1;
                continue;
            }

            $sameExpCount++;

            if ($sameExpCount > $this->base) {
                $newBucket = new Bucket(
                    $latestBucketForCurrExp->getNext()->getTimestamp(),
                    $latestBucketForCurrExp->getExponent() + 1
                );

                $this->replaceSlice($currentBucket, $latestBucketForCurrExp->getNext(), $newBucket);

                $currentBucket = $latestBucketForCurrExp = $newBucket;
                $sameExpCount = 1;
            }
        }

        return $this;
    }

    /**
     * @param Bucket $earliest
     * @param Bucket $latest
     * @param Bucket $replace
     * @return $this
     */
    private function replaceSlice(Bucket $earliest, Bucket $latest, Bucket $replace)
    {
        if ($next = $earliest->getNext()) {
            $next->setPrev($replace);
        }
        $replace->setNext($next);

        if ($prev = $latest->getPrev()) {
            $prev->setNext($replace);
        }
        $replace->setPrev($prev);

        if ($earliest === $this->getEarliestBucket()) {
            $this->earliestBucket = $replace;
        }

        if ($latest === $this->getLastBucket()) {
            $this->lastBucket = $replace;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $bucket = $this->getLastBucket();
        $pieces = array();

        while ($bucket) {
            $pieces[] = $bucket->getTimestamp() . ":" . pow($this->base, $bucket->getExponent());
            $bucket = $bucket->getNext();
        }

        return implode(", ", $pieces);
    }

    /**
     * @return bool
     */
    private function isEarliestBucketStale()
    {
        return $this->isEmpty()
            ? false
            : $this->getEarliestBucket()->getTimestamp() == $this->timestamp
        ;
    }
}