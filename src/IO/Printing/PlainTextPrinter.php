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

use Brickoo\Component\Validation\Argument;

/**
 * PlainTextPrinter
 *
 * Implementation of a plain text printer.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class PlainTextPrinter implements Printer {

    /** @const integer */
    const INDENT_TABS = "\t";
    const INDENT_SPACES = " ";

    /** @var \Brickoo\Component\IO\Printing\OutputPrinter */
    private $outputRenderer;

    /** @var string */
    private $eolSeparator;

    /** @var string */
    private $indentMode;

    /** @var integer */
    private $indentationAmount;

    /** @var string */
    private $bufferedTextLine;

    /**
     * Class constructor.
     * @param \Brickoo\Component\IO\Printing\OutputPrinter $outputRenderer
     * @param string $indentMode
     * @param string $eolSeparator
     * @throws \InvalidArgumentException
     */
    public function __construct(OutputPrinter $outputRenderer, $indentMode = self::INDENT_TABS, $eolSeparator = PHP_EOL) {
        Argument::isString($indentMode);
        Argument::isString($eolSeparator);
        $this->indentationAmount = 0;
        $this->bufferedTextLine = "";
        $this->outputRenderer = $outputRenderer;
        $this->eolSeparator = $eolSeparator;
        $this->indentMode = $indentMode;
    }

    /** {@inheritdoc} */
    public function nextLine() {
        $this->doPrint();
        $this->getOutputPrinter()->doPrint($this->eolSeparator);
        return $this;
    }

    /** {@inheritdoc} */
    public function indent($amount = 1) {
        Argument::isInteger($amount);

        if ($this->hasBufferedText()) {
            $this->addText($this->getIndentation($amount));
            return $this;
        }
        $this->indentationAmount += $amount;
        return $this;
    }

    /** {@inheritdoc} */
    public function outdent($amount = 1) {
        Argument::isInteger($amount);
        $this->indentationAmount -= $amount;
        $this->indentationAmount = $this->indentationAmount < 0
            ? 0 : $this->indentationAmount;
        return $this;
    }

    /** {@inheritdoc} */
    public function addText($text) {
        Argument::isString($text);

        if ((! $this->hasBufferedText()) && $this->indentationAmount > 0) {
            $this->bufferedTextLine .= $this->getIndentation($this->indentationAmount);
        }

        $this->bufferedTextLine .= $text;
        return $this;
    }

    /** {@inheritdoc} */
    public function doPrint() {
        if ($this->hasBufferedText()) {
            $this->getOutputPrinter()->doPrint($this->bufferedTextLine);
            $this->clearTextBuffer();
        }
        return $this;
    }

    /**
     * Return the output renderer.
     * @return \Brickoo\Component\IO\Printing\OutputPrinter
     */
    private function getOutputPrinter() {
        return $this->outputRenderer;
    }

    /**
     * Check if the buffer contains text.
     * @return boolean check result
     */
    private function hasBufferedText() {
        return (! empty($this->bufferedTextLine));
    }

    /**
     * Clear the text buffer.
     * @return \Brickoo\Component\IO\Printing\PlainTextPrinter
     */
    private function clearTextBuffer() {
        $this->bufferedTextLine = "";
        return $this;
    }

    /**
     * Return the indentation characters.
     * @param integer $amount
     * @return string the indentation characters
     */
    private function getIndentation($amount) {
        if ($this->indentMode == self::INDENT_SPACES) {
            $amount = $amount * 4;
        }
        return str_repeat($this->indentMode, $amount);
    }

}
