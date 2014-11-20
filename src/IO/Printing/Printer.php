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

/**
 * Printer
 *
 * Describes a printer with the possibility to format output
 * by using a fluid interface.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
interface Printer {

    /**
     * Print the buffered output.
     * Move cursor to next line.
     * @return \Brickoo\Component\IO\Printing\Printer
     */
    public function nextLine();

    /**
     * Indent text the amount of times.
     * On new lines the indentation is kept an can be increased.
     * @param integer $amount
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer
     */
    public function indent($amount = 1);

    /**
     * Outdent new lines the amount of times.
     * Does only affect new lines with indentation greater zero.
     * @param integer $amount
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer
     */
    public function outdent($amount = 1);

    /**
     * Adds the text to the printer buffer.
     * @param string $text
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer
     */
    public function addText($text);

    /**
     * Print buffered output with the output renderer dependency.
     * Clears the text buffer afterwards.
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IO\Printing\Printer
     */
    public function doPrint();

}
