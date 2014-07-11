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

/**
 * BufferRoutines
 *
 * Provides common buffer routines used
 * by buffered printer implementations.
 */
trait BufferRoutines {

    /** @var integer */
    private $bufferLength;

    /** @var string */
    private $buffer;

    /**
     * Return the buffered content.
     * @return string the buffer content
     */
    private function getBuffer() {
        return $this->buffer;
    }

    /**
     * Add content to the buffer.
     * @param string $content
     * @return \Brickoo\Component\IO\Printing\BufferRoutines
     */
    private function addToBuffer($content) {
        $this->buffer .= $content;
        return $this;
    }

    /**
     * Check if the buffer is turned off.
     * @return boolean check result
     */
    private function isBufferTurnedOff() {
        return $this->bufferLength <= 0;
    }

    /**
     * Check if the buffer length
     * is less than the argument.
     * @param integer $length
     * @return boolean check result
     */
    private function isBufferLessThan($length) {
        return $this->bufferLength < $length;
    }

    /**
     * Return the sum of the current buffer and argument.
     * @param string $output
     * @return integer the sum of the buffer and argument
     */
    private function sumBufferWith($output) {
        return strlen($this->buffer) + strlen($output);
    }

    /**
     * Clear the output buffer.
     * @return \Brickoo\Component\IO\Printing\OutputBufferedPrinter
     */
    private function clearBuffer() {
        $this->buffer = "";
        return $this;
    }

}
