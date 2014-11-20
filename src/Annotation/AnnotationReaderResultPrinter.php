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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\IO\Printing\OutputBufferedPrinter;
use Brickoo\Component\IO\Printing\PlainTextPrinter;
use Brickoo\Component\IO\Printing\Printable;
use Brickoo\Component\IO\Printing\Printer;

/**
 * AnnotationReaderResultPrinter
 *
 * Implementation of an annotation reader result printer.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReaderResultPrinter implements Printable {

    /** @var  \Brickoo\Component\IO\Printing\Printer */
    private $printer;

    /** @var \Brickoo\Component\Annotation\AnnotationReaderResult */
    private $annotationReaderResult;

    /** @param AnnotationReaderResult $annotationReaderResult */
    public function __construct(AnnotationReaderResult $annotationReaderResult) {
        $this->annotationReaderResult = $annotationReaderResult;
    }

    /** {@inheritdoc} */
    public function setPrinter(Printer $printer) {
        $this->printer = $printer;
        return $this;
    }

    /** {@inheritdoc} */
    public function getPrinter() {
        if (! $this->printer instanceof Printer) {
            $this->printer = new PlainTextPrinter(new OutputBufferedPrinter());
        }
        return $this->printer;
    }

    /** {@inheritdoc} */
    public function runPrinter() {
        $targets = [
            Annotation::TARGET_CLASS => "Class",
            Annotation::TARGET_METHOD => "Method",
            Annotation::TARGET_PROPERTY => "Property"
        ];

        foreach ($targets as $targetIdentifier => $targetName) {
            $this->printAnnotationsByTarget(
                $targetName,
                $this->annotationReaderResult->getAnnotationsByTarget($targetIdentifier)
            );
        }
        return $this;
    }

    /**
     * Print annotations by target with the printer.
     * @param string $targetName
     * @param \ArrayIterator $annotations
     * @return \Brickoo\Component\Annotation\AnnotationReaderResultPrinter
     */
    private function printAnnotationsByTarget($targetName, \ArrayIterator $annotations) {
        foreach ($annotations as $annotation) {
            $this->getPrinter()
                ->addText(sprintf("Annotation => @%s [%s] %s",
                    $annotation->getName(), $targetName, $annotation->getTargetLocation()))
                ->nextLine()
                ->indent(2);
            foreach ($annotation->getValues() as $param => $value) {
                $this->getPrinter()
                    ->addText(sprintf("param => (%s) %s", gettype($param), $param))
                    ->indent(2)
                    ->addText(sprintf("value => (%s) %s", gettype($value), str_replace("\n", "", var_export($value, true))))
                    ->nextLine();
            }

            $this->getPrinter()->outdent(2);
        }
        return $this;
    }

}
