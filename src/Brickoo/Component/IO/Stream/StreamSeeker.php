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

use Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException,
    Brickoo\Component\Validation\Argument;

/**
 * StreamSeeker
 *
 * Seeks to positions in supported streams.
 */
class StreamSeeker {

    /** @var resource */
    private $streamResource;

    /** @param resource $streamResource */
    public function __construct($streamResource) {
        Argument::IsResource($streamResource);
        $this->streamResource = $streamResource;
    }

    /**
     * Returns the current position of a file pointer.
     * @link http://www.php.net/manual/en/function.ftell.php
     * @throws \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @return integer or false on failure
     */
    public function tell() {
        if (! is_resource($this->streamResource)) {
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
        if (! is_resource($this->streamResource)) {
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
        Argument::IsInteger($offset);
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
        Argument::IsInteger($offset);
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
        Argument::IsInteger($offset);
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
        if (! is_resource($this->streamResource)) {
            throw new InvalidResourceHandleException();
        }
        return fseek($this->streamResource, $offset, $seekFlag) === 0;
    }

}
