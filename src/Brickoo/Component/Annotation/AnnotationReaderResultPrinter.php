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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\IO\Printing\OutputBufferedPrinter,
    Brickoo\Component\IO\Printing\PlainTextPrinter,
    Brickoo\Component\IO\Printing\Printable,
    Brickoo\Component\IO\Printing\Printer;

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

    /**
     * Return the printer.
     * @return \Brickoo\Component\IO\Printing\Printer
     */
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
    private function printAnnotationsByTarget($targetName, \ArrayIterator $annotations)  {
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
