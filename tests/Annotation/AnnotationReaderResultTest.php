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

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\AnnotationReaderResult;
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationReaderResult class.
 * @see Brickoo\Component\Annotation\AnnotationReaderResult
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AnnotationReaderResultTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::__construct
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getCollectionName
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getClassName
     *
     */
    public function testGetCollectionAndClassName() {
        $collectionName = "collection.name";
        $className = "\\Some\\Class\\Name";
        $annotationReaderResult = new AnnotationReaderResult($collectionName, $className);
        $this->assertEquals($collectionName, $annotationReaderResult->getCollectionName());
        $this->assertEquals($className, $annotationReaderResult->getClassName());
    }
    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::addAnnotation
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetValid
     */
    public function testAddAnnotation() {
        $annotation = $this->getAnnotationStub();
        $annotation->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue(Annotation::TARGET_CLASS));
        $annotationReaderResult = new AnnotationReaderResult("collection.name", "\\Some\\Class");
        $this->assertSame($annotationReaderResult, $annotationReaderResult->addAnnotation($annotation));
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::addAnnotation
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetValid
     * @covers Brickoo\Component\Annotation\Exception\InvalidTargetException
     * @expectedException \Brickoo\Component\Annotation\Exception\InvalidTargetException
     */
    public function testAddAnnotationThrowsInvalidTypeException() {
        $annotation = $this->getAnnotationStub();
        $annotation->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue("SOME_UNEXPECTED_TYPE"));
        $annotationReaderResult = new AnnotationReaderResult("collection.name", "\\Some\\Class");
        $annotationReaderResult->addAnnotation($annotation);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getAnnotationsByTarget
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetValid
     */
    public function testGetAnnotationsByTarget() {
        $annotation = $this->getAnnotationStub();
        $annotation->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue(Annotation::TARGET_CLASS));
        $annotationReaderResult = new AnnotationReaderResult("collection.name", "\\Some\\Class");
        $annotationReaderResult->addAnnotation($annotation);
        $annotationsIterator = $annotationReaderResult->getAnnotationsByTarget(Annotation::TARGET_CLASS);
        $this->assertInstanceOf("\\ArrayIterator", $annotationsIterator);
        $this->assertEquals(1, count($annotationsIterator));
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getAnnotationsByTarget
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetValid
     * @covers Brickoo\Component\Annotation\Exception\InvalidTargetException
     * @expectedException \Brickoo\Component\Annotation\Exception\InvalidTargetException
     */
    public function testGetAnnotationsByTargetThrowsInvalidTypeException() {
        $annotationReaderResult = new AnnotationReaderResult("collection.name", "\\Some\\Class");
        $annotationReaderResult->getAnnotationsByTarget(12345);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getIterator
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetValid
     */
    public function testGetIterator() {
        $annotationReaderResult = new AnnotationReaderResult("definition.name", "\\Some\\Class");
        $annotationsCollectionIterator = $annotationReaderResult->getIterator();
        $this->assertInstanceOf("\\ArrayIterator", $annotationsCollectionIterator);
        $this->assertEquals(0, count($annotationsCollectionIterator));

        $annotation = $this->getAnnotationStub();
        $annotation->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue(Annotation::TARGET_CLASS));
        $annotationReaderResult->addAnnotation($annotation);
        $annotationsCollectionIterator = $annotationReaderResult->getIterator();
        $this->assertEquals(1, count($annotationsCollectionIterator));
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

}
