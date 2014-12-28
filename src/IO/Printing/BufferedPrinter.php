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
