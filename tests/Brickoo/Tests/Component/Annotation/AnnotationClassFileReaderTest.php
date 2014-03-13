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

use Brickoo\Component\Annotation\AnnotationClassFileReader,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationClassFileReader class.
 * @see Brickoo\Component\Annotation\AnnotationClassFileReader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AnnotationClassFileReaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::__construct
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getReflectionClass
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::checkFileAvailability
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getNamespace
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getClassName
     */
    public function testGetAnnotations() {
        $definitionStub = $this->getDefinitionStub();
        $readerResult = $this->getAnnotationReaderResultStub();
        $reflectionReader = $this->getAnnotationReflectionReaderStub();
        $reflectionReader->expects($this->any())
                         ->method("getAnnotations")
                         ->with($definitionStub, $this->isInstanceOf("\\ReflectionClass"))
                         ->will($this->returnValue($readerResult));

        $fileReader = new AnnotationClassFileReader($reflectionReader);
        $this->assertSame(
            $readerResult,
            $fileReader->getAnnotations($definitionStub, __DIR__."/Assets/AnnotatedClass.php")
        );
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::checkFileAvailability
     * @covers Brickoo\Component\Annotation\Exception\FileDoesNotExistException
     * @expectedException \Brickoo\Component\Annotation\Exception\FileDoesNotExistException
     */
    public function testFileDoesNotExistThrowsException() {
        $definitionStub = $this->getDefinitionStub();
        $reflectionReader = $this->getAnnotationReflectionReaderStub();
        $fileReader = new AnnotationClassFileReader($reflectionReader);
        $fileReader->getAnnotations($definitionStub, __DIR__."/Assets/DoesNotExist.php");
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getReflectionClass
     * @covers Brickoo\Component\Annotation\Exception\UnableToLocateQualifiedClassNameException
     * @expectedException \Brickoo\Component\Annotation\Exception\UnableToLocateQualifiedClassNameException
     */
    public function testInvalidClassFileThrowsException() {
        $definitionStub = $this->getDefinitionStub();
        $reflectionReader = $this->getAnnotationReflectionReaderStub();
        $fileReader = new AnnotationClassFileReader($reflectionReader);
        $fileReader->getAnnotations($definitionStub, __DIR__."/Assets/InvalidClassFile.php");
    }

    /**
     * Returns an AnnotationReflectionClassReader stub.
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function getAnnotationReflectionReaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\AnnotationReflectionClassReader")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a Definition stub.
     * @return \Brickoo\Component\Annotation\Definition
     */
    private function getDefinitionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\Definition")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns an AnnotationReaderResult stub.
     * @return \Brickoo\Component\Annotation\AnnotationReaderResult
     */
    private function getAnnotationReaderResultStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\AnnotationReaderResult")
            ->disableOriginalConstructor()
            ->getMock();
    }

}