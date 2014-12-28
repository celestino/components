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

use Brickoo\Component\Annotation\AnnotationClassFileReader;
use PHPUnit_Framework_TestCase;

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
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::__construct
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getAnnotations
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getReflectionClass
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::checkFileAvailability
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getNamespace
     * @covers Brickoo\Component\Annotation\AnnotationClassFileReader::getClassName
     */
    public function testGetAnnotationsWithNamespaceInBraces() {
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
            $fileReader->getAnnotations($definitionStub, __DIR__."/Assets/AnnotatedClassWithNamespaceInBraces.php")
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
     * Returns a DefinitionCollection stub.
     * @return \Brickoo\Component\Annotation\Definition\DefinitionCollection
     */
    private function getDefinitionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\Definition\\DefinitionCollection")
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
