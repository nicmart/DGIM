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

/**
 * Class Counter
 * @package NicMart\DGIM
 */
class Counter
{
    /**
     * @var int
     */
    private $windowSize;

    /**
     * @var int
     */
    private $currentTime;

    /**
     * @var int
     */
    private $maxInteger;

    /**
     * @var int
     */
    private $bits;

    /**
     * @var BucketSequence[]
     */
    private $sequences = array();

    /**
     * @param $windowSize
     */
    public function __construct($windowSize, $maxInteger = 1, $maxForSameSize = 2)
    {
        $this->windowSize = $windowSize;
        $this->maxInteger = $maxInteger;
        $this->bits = (int) floor(log($maxInteger, 2)) + 1;
        $this->currentTime = 0;

        for ($i = 0; $i < $this->bits; $i++) {
            $this->sequences[] = new BucketSequence($windowSize, $maxForSameSize);
        }
    }

    /**
     * Get WindowSize
     *
     * @return mixed
     */
    public function getWindowSize()
    {
        return $this->windowSize;
    }

    /**
     * @param int $n
     * @return $this
     */
    public function input($n)
    {
        if ($n > $this->maxInteger) {
            throw new \OutOfBoundsException("The maximum integer supported is {$this->maxInteger}");
        }

        $binary = str_pad(decbin($n), $this->bits, "0", STR_PAD_LEFT);

        foreach ($this->sequences as $exponent => $sequence) {
            $sequence->input($binary[$this->bits - $exponent - 1]);
        }

        return $this;
    }

    /**
     * @param int $k
     * @return int
     */
    public function getCount($k)
    {
        $count = 0;

        foreach ($this->sequences as $index => $sequence) {
            $count += pow(2, $index) * $sequence->getCount($k);
        }

        return $count;
    }
}