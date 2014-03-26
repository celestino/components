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

use Brickoo\Component\Annotation\AnnotationReflectionClassReader,
    Brickoo\Component\Annotation\AnnotationTargetTypes,
    PHPUnit_Framework_TestCase,
    ReflectionClass;

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
     */
    public function testGetAnnotations() {
        include_once __DIR__."/Assets/AnnotatedClass.php";
        $annotationTarget = $this->getAnnotationTargetStub();
        $annotationTarget->expects($this->any())
                         ->method("getType")
                         ->will($this->returnValue(AnnotationTargetTypes::TYPE_CLASS));
        $annotationCollection = $this->getAnnotationCollectionStub();
        $annotationCollection->expects($this->any())
                             ->method("getTarget")
                             ->will($this->returnValue($annotationTarget));
        $annotationParser = $this->getAnnotationParserStub();
        $annotationParser->expects($this->any())
                         ->method("parse")
                         ->will($this->returnValue($annotationCollection));

        $classReader = new AnnotationReflectionClassReader($annotationParser);
        $result = $classReader->getAnnotations(
            ($definitionFixture = include __DIR__."/Assets/DefinitionFixture.php"),
            new ReflectionClass("\\Brickoo\\Tests\\Component\\Annotation\\Assets\\AnnotatedClass")
        );
        $this->assertInstanceOf("\\Brickoo\\Component\\Annotation\\AnnotationClassReaderResult", $result);

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

    /**
     * Returns an AnnotationCollection stub.
     * @return \Brickoo\Component\Annotation\AnnotationCollection
     */
    private function getAnnotationCollectionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\AnnotationCollection")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an AnnotationTarget stub.
     * @return \Brickoo\Component\Annotation\AnnotationTarget
     */
    private function getAnnotationTargetStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\AnnotationTarget")
            ->disableOriginalConstructor()
            ->getMock();
    }

}