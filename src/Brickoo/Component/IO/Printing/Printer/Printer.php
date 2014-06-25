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

namespace Brickoo\Component\IO\Printing\Printer;

use Brickoo\Component\IO\Printing\Renderer\OutputRenderer;

/**
 * Printer
 *
 * Describes a printer with the possibility to format output
 * by using a fluid interface.
 */
interface Printer {

    /**
     * Print the buffered output.
     * Move cursor to next line.
     * @return \Brickoo\Component\IO\Printing\Printer\Printer
     */
    public function nextLine();

    /**
     * Indent text the amount of times.
     * On new lines the indentation is kept an can be increased.
     * @param integer $amount
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer\Printer
     */
    public function indent($amount = 1);

    /**
     * Outdent new lines the amount of times.
     * Does only affect new lines with indentation greater zero.
     * @param integer $amount
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer\Printer
     */
    public function outdent($amount = 1);

    /**
     * Adds the text to the printer buffer.
     * @param string $text
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer\Printer
     */
    public function addText($text);

    /**
     * Print buffered output with the output renderer dependency.
     * Clears the text buffer afterwards.
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer\Printer
     */
    public function doPrint();

}
