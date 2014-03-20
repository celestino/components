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

namespace Brickoo\Tests\Component\Annotation;

use Brickoo\Component\Annotation\ReaderResultValidator,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the ReaderResultValidator class.
 * @see Brickoo\Component\Annotation\ReaderResultValidator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ReaderResultValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::validate
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::validateCollections
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getRequiredAnnotationsDefinitions
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::collectRequiredAnnotations
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getReadAnnotationsParameters
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::collectReadAnnotations
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::checkAnnotationRequirements
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::hasRequiredAnnotation
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getMissingParameters
     */
    public function testValidate() {
        $validator = new ReaderResultValidator();
        $validator->validate(
            include __DIR__."/Assets/DefinitionFixture.php",
            include __DIR__."/Assets/ReaderResultFixture.php"
        );
        $this->assertInstanceOf("\\Brickoo\\Component\\Annotation\\ReaderResultValidator", $validator);
    }

    /**
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::validate
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::validateCollections
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getRequiredAnnotationsDefinitions
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::collectRequiredAnnotations
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getReadAnnotationsParameters
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::collectReadAnnotations
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::checkAnnotationRequirements
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::hasRequiredAnnotation
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getMissingParameters
     * @covers Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException
     * @expectedException \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException
     */
    public function testValidateThrowsMissingAnnotationException() {
        $validator = new ReaderResultValidator();
        $validator->validate(
            include __DIR__."/Assets/DefinitionFixture.php",
            include __DIR__."/Assets/MissingAnnotationReaderResultFixture.php"
        );
    }

    /**
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::validate
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::validateCollections
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getRequiredAnnotationsDefinitions
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::collectRequiredAnnotations
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getReadAnnotationsParameters
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::collectReadAnnotations
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::checkAnnotationRequirements
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::hasRequiredAnnotation
     * @covers Brickoo\Component\Annotation\ReaderResultValidator::getMissingParameters
     * @covers Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException
     * @expectedException \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException
     */
    public function testValidateThrowsMissingParameterException() {
        $validator = new ReaderResultValidator();
        $validator->validate(
            include __DIR__."/Assets/DefinitionFixture.php",
            include __DIR__."/Assets/MissingParameterReaderResultFixture.php"
        );
    }

}
