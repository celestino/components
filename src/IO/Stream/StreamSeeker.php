<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\IO\Stream;

use Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException;
use Brickoo\Component\Common\Assert;

/**
 * StreamSeeker
 *
 * Seeks to positions in supported streams.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamSeeker {

    /** @var resource */
    private $streamResource;

    /** @param resource $streamResource */
    public function __construct($streamResource) {
        Assert::isResource($streamResource);
        $this->streamResource = $streamResource;
    }

    /**
     * Returns the current position of a file pointer.
     * @link http://www.php.net/manual/en/function.ftell.php
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return integer or false on failure
     */
    public function tell() {
        if (!is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }
        return ftell($this->streamResource);
    }

    /**
     * Rewinds the position of a file pointer.
     * http://www.php.net/manual/en/function.rewind.php
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return boolean success or failure
     */
    public function rewind() {
        if (!is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }
        return rewind($this->streamResource);
    }

    /**
     * Moves the stream pointer to the offset.
     * @param integer $offset
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return boolean success of movement
     */
    public function seekTo($offset) {
        Assert::isInteger($offset);
        return $this->processSeek($offset, SEEK_SET);
    }

    /**
     * Moves the stream pointer from the current
     * position to the offset.
     * @param integer $offset
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return boolean success of movement
     */
    public function seek($offset) {
        Assert::isInteger($offset);
        return $this->processSeek($offset, SEEK_CUR);
    }

    /**
     * Moves the stream pointer from the end
     * position forward to the offset.
     * @param integer $offset
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return boolean success of movement
     */
    public function seekEnd($offset) {
        Assert::isInteger($offset);
        return $this->processSeek($offset, SEEK_END);
    }

    /**
     * Seeks the stream pointer to an offset.
     * @param integer $offset
     * @param integer $seekFlag
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return boolean success of movement
     */
    private function processSeek($offset, $seekFlag) {
        if (!is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }
        return fseek($this->streamResource, $offset, $seekFlag) === 0;
    }

}
