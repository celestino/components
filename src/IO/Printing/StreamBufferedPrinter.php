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

namespace Brickoo\Component\IO\Printing;

use Brickoo\Component\IO\Stream\Stream;
use Brickoo\Component\IO\Stream\StreamWriter;
use Brickoo\Component\Validation\Argument;

/**
 * StreamPrinter
 *
 * Implements a buffered stream printer.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamBufferedPrinter extends BufferedPrinter {

    /** @const integer */
    const MAX_RETRIES = 1;

    /** @var \Brickoo\Component\IO\Stream\Stream */
    private $stream;

    /** @var \Brickoo\Component\IO\Stream\StreamWriter */
    private $streamWriter;

    /**
     * @param \Brickoo\Component\IO\Stream\Stream $stream
     * @param integer $bufferLength default buffer length 255 bytes
     * @throws \InvalidArgumentException
     */
    public function __construct(Stream $stream, $bufferLength = 255) {
        Argument::isInteger($bufferLength);
        $this->stream = $stream;
        $this->initializeBuffer($bufferLength);
    }

    /** {@inheritdoc} */
    protected function output($content) {
        $this->getStreamWriter()->write($content);
        return $this;
    }

    /**
     * Lazy initialization of the stream writer dependency.
     * Refresh the used stream resource if loaded.
     * @return \Brickoo\Component\IO\Stream\StreamWriter
     */
    private function getStreamWriter() {
        $streamResource = $this->stream->open();

        if ($this->streamWriter === null) {
            $this->streamWriter = new StreamWriter($streamResource, static::MAX_RETRIES);
            return $this->streamWriter;
        }

        return $this->streamWriter->refreshResource($streamResource);
    }

}
