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
    private $base = 2;

    /**
     * @var int
     */
    private $windowSize;

    /**
     * The timestamp of the next input.
     * It will be stored modulo windowSize
     * @var int
     */
    private $timestamp = -1;

    /**
     * @var int
     */
    private $maxForSameSize;

    /**
     * @param int $windowSize
     * @param int $maxForSameSize
     * @internal param int $base
     */
    public function __construct($windowSize, $maxForSameSize = 2)
    {
        $this->windowSize = $windowSize;
        $this->maxForSameSize = $maxForSameSize;
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
        $this->timestamp = ($this->timestamp + 1) % $this->windowSize;

        if ($this->isEarliestBucketStale()) {
            $this->removeEarliestBucket();
        }

        if ($bit) {
            $this->pushBucket(new Bucket($this->timestamp, 0));
            $this->condensateBuckets();
        }

        return $this;
    }

    /**
     * Returns an estimate for the number of 1s in the last k entries
     *
     * @param $k
     *
     * @return float|int|number
     */
    public function getCount($k)
    {
        $bucket = $this->findFirstEarliestBucketIntersectingInterval($k);
        $count = 0;

        if (!$bucket) {
            return $count;
        }

        $count += ceil(pow($this->base, $bucket->getExponent()) / 2);

        while ($bucket = $bucket->getPrev()) {
            $count += pow($this->base, $bucket->getExponent());
        }

        return (int) $count;
    }

    /**
     * @param int $intervalSize
     * @return Bucket|null
     */
    private function findFirstEarliestBucketIntersectingInterval($intervalSize)
    {
        $bucket = $this->earliestBucket;

        while ($bucket) {
            if ($this->getTimeIntervalFromNow($bucket->getTimestamp()) < $intervalSize) {
                return $bucket;
            }
            $bucket = $bucket->getPrev();
        }

        return null;
    }

    /**
     * Given a moduled $timestamp, return the distance between
     * that timestamp and the current timestamp
     * @param $k
     * @return int
     */
    private function getTimeIntervalFromNow($k)
    {
        return $this->positiveModule($this->timestamp - $k, $this->windowSize);
    }

    /**
     * @param int $n
     * @param int $m
     * @return int
     */
    private function positiveModule($n, $m)
    {
        $r = $n % $m;

        if ($r < 0) {
            $r = $r + $m;
        }

        return $r;
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

            if ($sameExpCount > $this->maxForSameSize) {
                $newBucket = new Bucket(
                    $currentBucket->getPrev()->getTimestamp(),
                    $currentBucket->getExponent() + 1
                );

                $this->replaceSlice(
                    $currentBucket,
                    $currentBucket->getPrev($this->base - 1),
                    $newBucket
                );

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
        $replace
            ->setNext($earliest->getNext())
            ->setPrev($latest->getPrev())
        ;

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