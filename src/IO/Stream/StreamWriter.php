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

namespace Brickoo\Component\IO\Stream;

use Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException;
use Brickoo\Component\IO\Stream\Exception\UnableToWriteBytesException;
use Brickoo\Component\Validation\Argument;

/**
 * StreamWriter
 *
 * Implements a stream writer who
 * does use a max. number of retries to write
 * the content to the resource.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamWriter {

    /** @var resource */
    private $streamResource;

    /** @var integer */
    private $numberOfRetries;

    /**
     * @param resource $streamResource
     * @param integer $numberOfRetries
     * @throws \InvalidArgumentException
     */
    public function __construct($streamResource, $numberOfRetries = 3) {
        Argument::isResource($streamResource);
        Argument::isInteger($numberOfRetries);
        $this->streamResource = $streamResource;
        $this->numberOfRetries = $numberOfRetries;
    }

    /**
     * Refresh the local stream resource.
     * @param resource $streamResource
     * @return \Brickoo\Component\IO\Stream\StreamWriter
     */
    public function refreshResource($streamResource) {
        Argument::isResource($streamResource);
        $this->streamResource = $streamResource;
        return $this;
    }

    /**
     * Writes the content to the stream resource.
     * @param string $content
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @throws \Brickoo\Component\IO\Stream\Exception\UnableToWriteBytesException
     * @return \Brickoo\Component\IO\Stream\StreamWriter
     */
    public function write($content) {
        if (! is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }

        if (($bytesLeft = $this->writeWithRetryLoop($this->streamResource, $content, $this->numberOfRetries)) > 0) {
            throw new UnableToWriteBytesException($bytesLeft);
        }

        return $this;
    }

    /**
     * Writes the content with a retry loop to the stream resource.
     * @param resource $streamResource
     * @param string $content
     * @param integer $retries
     * @return integer the unwritten bytes
     */
    private function writeWithRetryLoop($streamResource, $content, $retries) {
        $bytesLength = strlen($content);
        $bytesLeft = $bytesLength;

        while ($bytesLeft > 0 && $retries > 0) {
            $offset = $bytesLength - $bytesLeft;
            if (! ($bytesWritten = fwrite($streamResource, substr($content, $offset), $bytesLeft))) {
                --$retries;
                continue;
            }

            $bytesLeft -= $bytesWritten;
        }
        return (int)$bytesLeft;
    }

}
