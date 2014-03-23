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

use Brickoo\Component\Annotation\AnnotationClassReaderResult,
    Brickoo\Component\Annotation\AnnotationTargetTypes,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationClassReaderResult class.
 * @see Brickoo\Component\Annotation\AnnotationClassReaderResult
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AnnotationClassReaderResultTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::__construct
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::getDefinitionName
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::getClassName
     *
     */
    public function testgetDefinitionAndClassName() {
        $definitionName = "definition.name";
        $className = "\\Some\\Class\\Name";
        $AnnotationClassReaderResult = new AnnotationClassReaderResult($definitionName, $className);
        $this->assertEquals($definitionName, $AnnotationClassReaderResult->getDefinitionName());
        $this->assertEquals($className, $AnnotationClassReaderResult->getClassName());
    }
    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::addCollection
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::isTargetTypeValid
     */
    public function testAddCollection() {
        $annotationTarget = $this->getAnnotationTargetStub();
        $annotationTarget->expects($this->any())
                         ->method("getType")
                         ->will($this->returnValue(AnnotationTargetTypes::TYPE_CLASS));
        $collection = $this->getAnnotationCollectionStub();
        $collection->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue($annotationTarget));
        $AnnotationClassReaderResult = new AnnotationClassReaderResult("definition.name", "\\Some\\Class");
        $this->assertSame($AnnotationClassReaderResult, $AnnotationClassReaderResult->addCollection($collection));
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::addCollection
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::isTargetTypeValid
     * @covers Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     * @expectedException \Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     */
    public function testAddCollectionThrowsInvalidTypeException() {
        $annotationTarget = $this->getAnnotationTargetStub();
        $annotationTarget->expects($this->any())
                         ->method("getType")
                         ->will($this->returnValue("SOME_UNEXPECTED_TYPE"));
        $collection = $this->getAnnotationCollectionStub();
        $collection->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue($annotationTarget));
        $AnnotationClassReaderResult = new AnnotationClassReaderResult("definition.name", "\\Some\\Class");
        $AnnotationClassReaderResult->addCollection($collection);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::getCollectionsByTargetType
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::isTargetTypeValid
     */
    public function testGetCollectionsByTargetType() {
        $annotationTarget = $this->getAnnotationTargetStub();
        $annotationTarget->expects($this->any())
                         ->method("getType")
                         ->will($this->returnValue(AnnotationTargetTypes::TYPE_CLASS));
        $collection = $this->getAnnotationCollectionStub();
        $collection->expects($this->any())
                   ->method("getTarget")
                   ->will($this->returnValue($annotationTarget));
        $AnnotationClassReaderResult = new AnnotationClassReaderResult("definition.name", "\\Some\\Class");
        $AnnotationClassReaderResult->addCollection($collection);
        $annotationsCollectionIterator = $AnnotationClassReaderResult->getCollectionsByTargetType(AnnotationTargetTypes::TYPE_CLASS);
        $this->assertInstanceOf("\\ArrayIterator", $annotationsCollectionIterator);
        $this->assertEquals(1, count($annotationsCollectionIterator));
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::getCollectionsByTargetType
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::isTargetTypeValid
     * @covers Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     * @expectedException \Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     */
    public function testGetCollectionsByTargetTypeThrowsInvalidTypeException() {
        $AnnotationClassReaderResult = new AnnotationClassReaderResult("definition.name", "\\Some\\Class");
        $AnnotationClassReaderResult->getCollectionsByTargetType(12345);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::getIterator
     * @covers Brickoo\Component\Annotation\AnnotationClassReaderResult::isTargetTypeValid
     */
    public function testGetIterator() {
        $AnnotationClassReaderResult = new AnnotationClassReaderResult("definition.name", "\\Some\\Class");
        $annotationsCollectionIterator = $AnnotationClassReaderResult->getIterator();
        $this->assertInstanceOf("\\ArrayIterator", $annotationsCollectionIterator);
        $this->assertEquals(3, count($annotationsCollectionIterator));
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
