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

namespace Brickoo\Tests\Component\Annotation;

use Brickoo\Component\Annotation\AnnotationReaderResultValidator;
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationReaderResultValidator class.
 * @see Brickoo\Component\Annotation\AnnotationReaderResultValidator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReaderResultValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::validate
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::validateAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getRequiredAnnotationsParameters
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getAnnotationsValues
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::checkAnnotationRequirements
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::hasRequiredAnnotation
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getMissingParameters
     */
    public function testValidate() {
        $validator = new AnnotationReaderResultValidator();
        $validator->validate(
            include __DIR__ . "/Assets/AnnotationDefinitionCollectionFixture.php",
            include __DIR__."/Assets/ReaderResultFixture.php"
        );
        $this->assertInstanceOf("\\Brickoo\\Component\\Annotation\\AnnotationReaderResultValidator", $validator);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::validate
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::validateAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getRequiredAnnotationsParameters
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getAnnotationsValues
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::checkAnnotationRequirements
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::hasRequiredAnnotation
     * @covers Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException
     * @expectedException \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException
     */
    public function testValidateThrowsMissingAnnotationException() {
        $validator = new AnnotationReaderResultValidator();
        $validator->validate(
            include __DIR__ . "/Assets/AnnotationDefinitionCollectionFixture.php",
            include __DIR__."/Assets/MissingAnnotationReaderResultFixture.php"
        );
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::validate
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::validateAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getRequiredAnnotationsParameters
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getAnnotationsValues
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::checkAnnotationRequirements
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::hasRequiredAnnotation
     * @covers Brickoo\Component\Annotation\AnnotationReaderResultValidator::getMissingParameters
     * @covers Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException
     * @expectedException \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException
     */
    public function testValidateThrowsMissingParameterException() {
        $validator = new AnnotationReaderResultValidator();
        $validator->validate(
            include __DIR__ . "/Assets/AnnotationDefinitionCollectionFixture.php",
            include __DIR__."/Assets/MissingParameterReaderResultFixture.php"
        );
    }

}
