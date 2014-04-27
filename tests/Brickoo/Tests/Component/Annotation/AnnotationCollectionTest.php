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

use Brickoo\Component\Annotation\AnnotationCollection,
    Brickoo\Component\Annotation\AnnotationTarget,
    Brickoo\Component\Annotation\AnnotationTargetTypes,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationCollection class.
 * @see Brickoo\Component\Annotation\AnnotationCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AnnotationCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationCollection::__construct
     * @covers Brickoo\Component\Annotation\AnnotationCollection::getTarget
     */
    public function testGetTarget() {
        $annotationsTarget = $this->getAnnotationsTargetStub();
        $collection = new AnnotationCollection($annotationsTarget);
        $this->assertSame($annotationsTarget, $collection->getTarget());
    }

    /** @covers Brickoo\Component\Annotation\AnnotationCollection::getTargetType */
    public function testGetTargetType() {
        $collectionType = AnnotationTargetTypes::TYPE_CLASS;
        $annotationsTarget = $this->getAnnotationsTargetStub();
        $annotationsTarget->expects($this->any())
                          ->method("getType")
                          ->will($this->returnValue($collectionType));
        $collection = new AnnotationCollection($annotationsTarget);
        $this->assertEquals($collectionType, $collection->getTargetType());
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationCollection::getTarget
     * @covers Brickoo\Component\Annotation\AnnotationCollection::isTypeOf
     */
    public function testTargetIsTypeOf() {
        $targetType = AnnotationTarget::TYPE_CLASS;
        $annotationsTarget = $this->getAnnotationsTargetStub();
        $annotationsTarget->expects($this->any())
                          ->method("isTypeOf")
                          ->with($targetType)
                          ->will($this->returnValue(true));
        $collection = new AnnotationCollection($annotationsTarget);
        $this->assertTrue($collection->isTypeOf($targetType));
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationCollection::push
     * @covers Brickoo\Component\Annotation\AnnotationCollection::shift
     */
    public function testPushAndShiftAnnotationDefinitionFromCollection() {
        $annotation1 = $this->getAnnotationStub();
        $annotation2 = $this->getAnnotationStub();
        $collection = new AnnotationCollection($this->getAnnotationsTargetStub());
        $this->assertSame($collection, $collection->push($annotation1));
        $this->assertSame($collection, $collection->push($annotation2));
        $this->assertSame($annotation1, $collection->shift());
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationCollection::shift
     * @covers Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @expectedException \Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     */
    public function testShiftWithEmptyCollectionThrowsException() {
        $collection = new AnnotationCollection($this->getAnnotationsTargetStub());
        $collection->shift();
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationCollection::push
     * @covers Brickoo\Component\Annotation\AnnotationCollection::pop
     */
    public function testPushAndPopAnnotationDefinitionFromCollection() {
        $annotation1 = $this->getAnnotationStub();
        $annotation2 = $this->getAnnotationStub();
        $collection = new AnnotationCollection($this->getAnnotationsTargetStub());
        $this->assertSame($collection, $collection->push($annotation1));
        $this->assertSame($collection, $collection->push($annotation2));
        $this->assertSame($annotation2, $collection->pop());
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationCollection::pop
     * @covers Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @expectedException \Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     */
    public function testPopWithEmptyCollectionThrowsException() {
        $collection = new AnnotationCollection($this->getAnnotationsTargetStub());
        $collection->pop();
    }

    /** @covers Brickoo\Component\Annotation\AnnotationCollection::getIterator */
    public function testGetCollectionArrayIterator() {
        $collection = new AnnotationCollection($this->getAnnotationsTargetStub());
        $this->assertInstanceOf("\\ArrayIterator", $collection->getIterator());
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationCollection::isEmpty
     * @covers Brickoo\Component\Annotation\AnnotationCollection::count
     */
    public function testIsEmptyCollectionAndCountItems() {
        $annotation = $this->getAnnotationStub();
        $collection = new AnnotationCollection($this->getAnnotationsTargetStub());
        $this->assertTrue($collection->isEmpty());
        $this->assertEquals(0, count($collection));
        $collection->push($annotation);
        $this->assertFalse($collection->isEmpty());
        $this->assertEquals(1, count($collection));

    }

    /** @covers Brickoo\Component\Annotation\AnnotationCollection::merge */
    public function testCollectionOfTheSameTypeCanBeMerged() {
        $annotation = $this->getAnnotationStub();
        $target = $this->getAnnotationsTargetStub();
        $target->expects($this->any())
               ->method("isTypeOf")
               ->with(AnnotationTarget::TYPE_CLASS)
               ->will($this->returnValue(true));
        $target->expects($this->any())
               ->method("getType")
               ->will($this->returnValue(AnnotationTarget::TYPE_CLASS));
        $collection_1 = new AnnotationCollection($target);
        $collection_1->push($annotation);
        $collection_2 = new AnnotationCollection($target);
        $collection_2->push($annotation);

        $collection_2->merge($collection_1);
        $this->assertEquals(2, count($collection_2));
    }

    /**
     * Returns an AnnotationsTarget stub.
     * @return \Brickoo\Component\Annotation\AnnotationTarget
     */
    private function getAnnotationsTargetStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\AnnotationTarget")
            ->disableOriginalConstructor()
            ->getMock();
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
