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
        Argument::IsResource($streamResource);
        Argument::IsInteger($numberOfRetries);
        $this->streamResource = $streamResource;
        $this->numberOfRetries = $numberOfRetries;
    }

    /**
     * Refresh the local stream resource.
     * @param resource $streamResource
     * @return \Brickoo\Component\IO\Stream\StreamWriter
     */
    public function refreshResource($streamResource) {
        Argument::IsResource($streamResource);
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

        while ($bytesLeft > 0) {
            $offset = $bytesLength - $bytesLeft;
            $bytesWritten = fwrite($streamResource, substr($content, $offset), $bytesLeft);

            if ($bytesWritten === false || $bytesWritten === 0) {
                --$retries;
                if ($retries == 0) {
                    break;
                }
                continue;
            }

            $bytesLeft -= $bytesWritten;
        }
        return (int)$bytesLeft;
    }

}
