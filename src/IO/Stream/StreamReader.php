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
use Brickoo\Component\IO\Stream\Exception\UnableToReadBytesException;
use Brickoo\Component\Validation\Argument;

/**
 * StreamReader
 *
 * Implements a stream reader.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamReader {

    /** @var resource */
    private $streamResource;

    /** @param resource $streamResource */
    public function __construct($streamResource) {
        Argument::isResource($streamResource);
        $this->streamResource = $streamResource;
    }

    /**
     * Return the read bytes from the stream.
     * @param integer $bytes
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return string the read content
     */
    public function read($bytes = 1024) {
        Argument::isInteger($bytes);

        if (! is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }

        return (string)fread($this->streamResource, $bytes);
    }

    /**
     * Returns a line after the current stream pointer position.
     * @throws \Brickoo\Component\IO\Stream\Exception\UnableToReadBytesException
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return string
     */
    public function readLine() {
        if (! is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }

        if (($content = fgets($this->streamResource)) === false) {
            throw new UnableToReadBytesException(fstat($this->streamResource)["size"]);
        }

        return $content;
    }

    /**
     * Returns the content of a file after the current stream pointer position.
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return string
     */
    public function readFile() {
        if (! is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }

        return (string)stream_get_contents($this->streamResource);
    }

}
