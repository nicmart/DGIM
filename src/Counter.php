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


class WindowCounter
{
    private $windowSize;

    private $currentTime;
    /**
     * @param $windowSize
     */
    public function __construct($windowSize)
    {
        $this->windowSize = $windowSize;
        $this->currentTime = 0;
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

    public function input($bit)
    {


        $this->currentTime = ($this->currentTime + 1) % $this->windowSize;
    }
}