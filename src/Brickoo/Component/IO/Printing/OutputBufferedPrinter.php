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

use Brickoo\Component\Validation\Argument;

/**
 * OutputBufferedPrinter
 *
 * Implementation of a printer for the output buffer.
 */
class OutputBufferedPrinter implements OutputPrinter {

    use BufferRoutines;

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
     * Destructor.
     * Output any buffered content before destruction
     * @return void
     */
    public function __destruct() {
        $this->output($this->getBuffer());
    }

    /**
     * Output the content.
     * @param string $content
     * @return \Brickoo\Component\IO\Printing\OutputBufferedPrinter
     */
    private function output($content) {
        echo $content;
        return $this;
    }

}
