<?php
/*
 * This file is part of DGIM.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NicMart\DGIM;

/**
 * Class Bucket
 *
 * A double linked list node with information for the timestamp and the current exponent
 */
class Bucket
{
    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var int
     */
    private $exponent;

    /**
     * @var Bucket
     */
    private $prev;

    /**
     * @var Bucket
     */
    private $next;

    /**
     * @param $recentEnd
     * @param $exponent
     */
    public function __construct($recentEnd, $exponent)
    {
        $this->timestamp = $recentEnd;
        $this->exponent = $exponent;
    }

    /**
     * Get Exponent
     *
     * @return mixed
     */
    public function getExponent()
    {
        return $this->exponent;
    }

    /**
     * Get RecentEnd
     *
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get Next
     *
     * @param int $index
     * @return Bucket
     */
    public function getNext($index = 1)
    {
        if ($index > 1) {
            $next = $this->getNext();
            return $next
                ? $next->getNext($index - 1)
                : null
            ;
        }

        return $this->next;
    }

    /**
     * Set Next
     *
     * @param Bucket $next
     *
     * @param bool $synchronized
     * @return Bucket The current instance
     */
    public function setNext(Bucket $next = null, $synchronized = true)
    {
        $this->next = $next;

        if ($next && $synchronized) {
            $next->setPrev($this, false);
        }

        return $this;
    }

    /**
     * Get Prev
     *
     * @param int $index
     * @return Bucket
     */
    public function getPrev($index = 1)
    {
        if ($index > 1) {
            $prev = $this->getPrev();
            return $prev
                ? $prev->getPrev($index - 1)
                : null
            ;
        }

        return $this->prev;
    }

    /**
     * Set Prev
     *
     * @param Bucket $prev
     *
     * @param bool $synchronized
     * @return Bucket The current instance
     */
    public function setPrev(Bucket $prev = null, $synchronized = true)
    {
        $this->prev = $prev;

        if ($prev && $synchronized) {
            $prev->setNext($this, false);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTimestamp() . " : " . pow(2, $this->getExponent());
    }
}