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
 * Class FirstClass
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
     * @return Bucket
     */
    public function getNext($index = 1)
    {
        if ($index > 1) {
            $next = $this->getNext();
            if (!$next) {
                throw new \UnderflowException("The element has no successor");
            }
            return $next->getNext($index - 1);
        }

        return $this->next;
    }

    /**
     * Set Next
     *
     * @param Bucket $next
     *
     * @return Bucket The current instance
     */
    public function setNext(Bucket $next = null)
    {
        $this->next = $next;
        return $this;
    }

    /**
     * Get Prev
     *
     * @return Bucket
     */
    public function getPrev($index = 1)
    {
        if ($index > 1) {
            $prev = $this->getPrev();
            if (!$prev) {
                throw new \UnderflowException("The element has no previous elements");
            }
            return $prev->getPrev($index - 1);
        }

        return $this->prev;
    }

    /**
     * Set Prev
     *
     * @param Bucket $prev
     *
     * @return Bucket The current instance
     */
    public function setPrev(Bucket $prev = null)
    {
        $this->prev = $prev;
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