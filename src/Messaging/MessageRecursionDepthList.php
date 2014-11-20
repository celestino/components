<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Component\Messaging;

use Brickoo\Component\Common\Container;
use Brickoo\Component\Validation\Argument;

/**
 * MessageRecursionDepthList
 *
 * Implements a list for handling message recursion depth.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageRecursionDepthList extends Container {

    /** @var integer */
    private $recursionDepthLimit;

    /**
     * Class constructor.
     * @param integer $recursionDepthLimit default 5
     */
    public function __construct($recursionDepthLimit = 5) {
        Argument::isInteger($recursionDepthLimit);
        $this->recursionDepthLimit = $recursionDepthLimit;
        parent::__construct();
    }

    /**
     * Adds an unique message to the list.
     * @param string $messageName
     * @return \Brickoo\Component\Messaging\MessageRecursionDepthList
     */
    public function addMessage($messageName) {
        Argument::isString($messageName);
        $this->set($messageName, 0);
        return $this;
    }

    /**
     * Returns the current message recursion depth.
     * @param string $messageName
     * @return integer the message recursion depth
     */
    public function getRecursionDepth($messageName) {
        Argument::isString($messageName);
        return $this->get($messageName, 0);
    }

    /**
     * Checks if the message recursion depth limit has been reached.
     * @param string $messageName
     * @return boolean check result
     */
    public function isDepthLimitReached($messageName) {
        Argument::isString($messageName);
        return $this->contains($messageName)
            && $this->get($messageName) >= $this->recursionDepthLimit;
    }

    /**
     * Increases the current message recursion depth.
     * @param string $messageName
     * @return \Brickoo\Component\Messaging\MessageRecursionDepthList
     */
    public function increaseDepth($messageName) {
        Argument::isString($messageName);
        $depth = $this->getRecursionDepth($messageName);
        $this->set($messageName, ++$depth);
        return $this;
    }

    /**
     * Decreases the current message recursion depth.
     * @param string $messageName
     * @return \Brickoo\Component\Messaging\MessageRecursionDepthList
     */
    public function decreaseDepth($messageName) {
        Argument::isString($messageName);
        $depth = $this->getRecursionDepth($messageName);
        $this->set($messageName, --$depth);
        return $this;
    }

}
