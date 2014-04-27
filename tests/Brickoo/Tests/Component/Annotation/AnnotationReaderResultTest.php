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

use Brickoo\Component\Annotation\AnnotationReaderResult,
    Brickoo\Component\Annotation\AnnotationTargetTypes,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationReaderResult class.
 * @see Brickoo\Component\Annotation\AnnotationReaderResult
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AnnotationReaderResultTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::__construct
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getDefinitionName
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getClassName
     *
     */
    public function testgetDefinitionAndClassName() {
        $definitionName = "definition.name";
        $className = "\\Some\\Class\\Name";
        $AnnotationReaderResult = new AnnotationReaderResult($definitionName, $className);
        $this->assertEquals($definitionName, $AnnotationReaderResult->getDefinitionName());
        $this->assertEquals($className, $AnnotationReaderResult->getClassName());
    }
    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::addCollection
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetTypeValid
     */
    public function testAddCollection() {
        $collection = $this->getAnnotationCollectionStub();
        $collection->expects($this->any())
                   ->method("getTargetType")
                   ->will($this->returnValue(AnnotationTargetTypes::TYPE_CLASS));
        $AnnotationReaderResult = new AnnotationReaderResult("definition.name", "\\Some\\Class");
        $this->assertSame($AnnotationReaderResult, $AnnotationReaderResult->addCollection($collection));
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::addCollection
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetTypeValid
     * @covers Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     * @expectedException \Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     */
    public function testAddCollectionThrowsInvalidTypeException() {
        $collection = $this->getAnnotationCollectionStub();
        $collection->expects($this->any())
                   ->method("getTargetType")
                   ->will($this->returnValue("SOME_UNEXPECTED_TYPE"));
        $AnnotationReaderResult = new AnnotationReaderResult("definition.name", "\\Some\\Class");
        $AnnotationReaderResult->addCollection($collection);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getCollectionsByTargetType
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetTypeValid
     */
    public function testGetCollectionsByTargetType() {
        $collection = $this->getAnnotationCollectionStub();
        $collection->expects($this->any())
                   ->method("getTargetType")
                   ->will($this->returnValue(AnnotationTargetTypes::TYPE_CLASS));
        $AnnotationReaderResult = new AnnotationReaderResult("definition.name", "\\Some\\Class");
        $AnnotationReaderResult->addCollection($collection);
        $annotationsCollectionIterator = $AnnotationReaderResult->getCollectionsByTargetType(AnnotationTargetTypes::TYPE_CLASS);
        $this->assertInstanceOf("\\ArrayIterator", $annotationsCollectionIterator);
        $this->assertEquals(1, count($annotationsCollectionIterator));
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getCollectionsByTargetType
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetTypeValid
     * @covers Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     * @expectedException \Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     */
    public function testGetCollectionsByTargetTypeThrowsInvalidTypeException() {
        $AnnotationReaderResult = new AnnotationReaderResult("definition.name", "\\Some\\Class");
        $AnnotationReaderResult->getCollectionsByTargetType(12345);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::getIterator
     * @covers Brickoo\Component\Annotation\AnnotationReaderResult::isTargetTypeValid
     */
    public function testGetIterator() {
        $AnnotationReaderResult = new AnnotationReaderResult("definition.name", "\\Some\\Class");
        $annotationsCollectionIterator = $AnnotationReaderResult->getIterator();
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

}
