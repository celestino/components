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
 * Implements an abstract buffered printer.
 * The main purpose is to buffer a defined
 * length of bytes before calling the print routine.
 */
abstract class BufferedPrinter implements OutputPrinter {

    /** @var integer */
    protected $bufferLength;

    /** @var string */
    protected $buffer;

    /** {@inheritdoc} */
    public function doPrint($output) {
        if ($this->isBufferTurnedOff()) {
            $this->output($output);
            return $this;
        }

        if ($this->isBufferLessThan($this->sumBufferWith($output))) {
            $this->output($this->getBuffer());
            $this->clearBuffer();
        }

        $this->addToBuffer($output);
        return $this;
    }

    /**
     * Flush the output buffer and clear the local buffer.
     * @return \Brickoo\Component\IO\Printing\OutputBufferedPrinter
     */
    public function flushBuffer() {
        $this->output($this->getBuffer())->clearBuffer();
        return $this;
    }

    /**
     * Initialize the buffer.
     * @param integer $bufferLength
     * @param string $bufferContent
     * @return \Brickoo\Component\IO\Printing\BufferedPrinter
     */
    protected function initializeBuffer($bufferLength = 255, $bufferContent = "") {
        $this->bufferLength = $bufferLength;
        $this->buffer = $bufferContent;
        return $this;
    }

    /**
     * Return the buffered content.
     * @return string the buffer content
     */
    protected function getBuffer() {
        return $this->buffer;
    }

    /**
     * Add content to the buffer.
     * @param string $content
     * @return \Brickoo\Component\IO\Printing\BufferedPrinter
     */
    protected function addToBuffer($content) {
        $this->buffer .= $content;
        return $this;
    }

    /**
     * Check if the buffer is turned off.
     * @return boolean check result
     */
    protected function isBufferTurnedOff() {
        return $this->bufferLength <= 0;
    }

    /**
     * Check if the buffer length
     * is less than the argument.
     * @param integer $length
     * @return boolean check result
     */
    protected function isBufferLessThan($length) {
        return $this->bufferLength < $length;
    }

    /**
     * Return the sum of the current buffer and argument.
     * @param string $output
     * @return integer the sum of the buffer and argument
     */
    protected function sumBufferWith($output) {
        return strlen($this->buffer) + strlen($output);
    }

    /**
     * Clear the output buffer.
     * @return \Brickoo\Component\IO\Printing\BufferedPrinter
     */
    protected function clearBuffer() {
        $this->buffer = "";
        return $this;
    }

    /**
     * Output the content using the concrete printer implementation.
     * @param string $content
     * @return \Brickoo\Component\IO\Printing\BufferedPrinter
     */
    abstract protected function output($content);

}