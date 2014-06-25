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

namespace Brickoo\Component\IO\Printing\Renderer;

use Brickoo\Component\Validation\Argument;

/**
 * OutputBufferRenderer
 *
 * Implementation of a renderer using the output buffer.
 */
class OutputBufferRenderer implements OutputRenderer {

    /** @var integer */
    private $bufferLength;

    /** @var string */
    private $buffer;

    /**
     * @param integer $bufferLength bellow or equal zero turns the buffer off
     * @throws \InvalidArgumentException
     */
    public function __construct($bufferLength = 0) {
        Argument::IsInteger($bufferLength);
        $this->buffer = "";
        $this->bufferLength = $bufferLength;
    }

    /** {@inheritdoc} */
    public function render($output) {
        if ($this->isBufferTurnedOff()) {
            $this->output($output);
            return $this;
        }

        if ($this->isBufferLessThan($this->sumBufferWith($output))) {
            $this->output($this->buffer);
            $this->clearBuffer();
        }

        $this->buffer .= $output;
        return $this;
    }

    /**
     * Destructor.
     * Output any buffered content before destruction
     * @return void
     */
    public function __destruct() {
        $this->output($this->buffer);
    }

    /**
     * Output the content.
     * @param string $content
     * @return \Brickoo\Component\IO\Printing\Renderer\OutputBufferRenderer
     */
    private function output($content) {
        echo $content;
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
     * @return \Brickoo\Component\IO\Printing\Renderer\OutputBufferRenderer
     */
    private function clearBuffer() {
        $this->buffer = "";
        return $this;
    }

}
