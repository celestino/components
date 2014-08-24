<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
