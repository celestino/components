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

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\AnnotationReflectionClassReader;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Test suite for the AnnotationReflectionClassReader class.
 * @see Brickoo\Component\Annotation\AnnotationReflectionClassReader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReflectionClassReaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::__construct
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::getAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::addClassAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::addMethodsAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::addPropertiesAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::getAnnotationsNames
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::setAnnotationWhiteList
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::parseAnnotationList
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::getReflectionMemberList
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::parseAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationReflectionClassReader::addResultAnnotations
     */
    public function testGetAnnotations() {
        include_once __DIR__."/Assets/AnnotatedClass.php";
        $definitionCollectionFixture = include __DIR__ . "/Assets/AnnotationDefinitionCollectionFixture.php";

        $annotation = $this->getAnnotationStub();
        $annotation->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue(Annotation::TARGET_CLASS));

        $annotationParser = $this->getAnnotationParserStub();
        $annotationParser->expects($this->any())
                         ->method("parse")
                         ->will($this->returnValue([$annotation]));

        $classReader = new AnnotationReflectionClassReader($annotationParser);
        $result = $classReader->getAnnotations(
            $definitionCollectionFixture,
            new ReflectionClass("\\Brickoo\\Tests\\Component\\Annotation\\Assets\\AnnotatedClass")
        );
        $this->assertInstanceOf("\\Brickoo\\Component\\Annotation\\AnnotationReaderResult", $result);
    }

    /**
     * Returns an Annotation stub.
     * @return \Brickoo\Component\Annotation\Annotation
     */
    private function getAnnotationStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\Annotation")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an AnnotationParser stub.
     * @return \Brickoo\Component\Annotation\AnnotationParser
     */
    private function getAnnotationParserStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\AnnotationParser")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
