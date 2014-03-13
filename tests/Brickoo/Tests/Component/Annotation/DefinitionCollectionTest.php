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

use Brickoo\Component\Annotation\AnnotationTargetTypes,
    Brickoo\Component\Annotation\DefinitionCollection,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the DefinitionCollection class.
 * @see Brickoo\Component\Annotation\DefinitionCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class DefinitionCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\DefinitionCollection::__construct
     * @covers Brickoo\Component\Annotation\DefinitionCollection::getTarget
     */
    public function testGetTarget() {
        $targetDefinition = $this->getTargetDefinitionStub();
        $definitionCollection = new DefinitionCollection($targetDefinition);
        $this->assertSame($targetDefinition, $definitionCollection->getTarget());
    }

    /**
     * @covers Brickoo\Component\Annotation\DefinitionCollection::getTarget
     * @covers Brickoo\Component\Annotation\DefinitionCollection::isTypeOf
     */
    public function testTargetIsTypeOf() {
        $targetType = AnnotationTargetTypes::TYPE_CLASS;
        $annotationsTarget = $this->getTargetDefinitionStub();
        $annotationsTarget->expects($this->any())
                          ->method("isTypeOf")
                          ->with($targetType)
                          ->will($this->returnValue(true));
        $definitionCollection = new DefinitionCollection($annotationsTarget);
        $this->assertTrue($definitionCollection->isTypeOf($targetType));
    }

    /**
     * @covers Brickoo\Component\Annotation\DefinitionCollection::push
     * @covers Brickoo\Component\Annotation\DefinitionCollection::shift
     */
    public function testPushAndShiftAnnotationDefinitionFromCollection() {
        $annotation1 = $this->getAnnotationDefinitionStub();
        $annotation2 = $this->getAnnotationDefinitionStub();
        $definitionCollection = new DefinitionCollection($this->getTargetDefinitionStub());
        $this->assertSame($definitionCollection, $definitionCollection->push($annotation1));
        $this->assertSame($definitionCollection, $definitionCollection->push($annotation2));
        $this->assertSame($annotation1, $definitionCollection->shift());
    }

    /**
     * @covers Brickoo\Component\Annotation\DefinitionCollection::shift
     * @covers Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @expectedException Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     */
    public function testShiftWithEmptyCollectionThrowsException() {
        $definitionCollection = new DefinitionCollection($this->getTargetDefinitionStub());
        $definitionCollection->shift();
    }

    /**
     * @covers Brickoo\Component\Annotation\DefinitionCollection::push
     * @covers Brickoo\Component\Annotation\DefinitionCollection::pop
     */
    public function testPushAndPopAnnotationDefinitionFromCollection() {
        $annotation1 = $this->getAnnotationDefinitionStub();
        $annotation2 = $this->getAnnotationDefinitionStub();
        $definitionCollection = new DefinitionCollection($this->getTargetDefinitionStub());
        $this->assertSame($definitionCollection, $definitionCollection->push($annotation1));
        $this->assertSame($definitionCollection, $definitionCollection->push($annotation2));
        $this->assertSame($annotation2, $definitionCollection->pop());
    }

    /**
     * @covers Brickoo\Component\Annotation\DefinitionCollection::pop
     * @covers Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @expectedException Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     */
    public function testPopWithEmptyCollectionThrowsException() {
        $definitionCollection = new DefinitionCollection($this->getTargetDefinitionStub());
        $definitionCollection->pop();
    }

    /** @covers Brickoo\Component\Annotation\DefinitionCollection::getIterator */
    public function testGetCollectionArrayIterator() {
        $definitionCollection = new DefinitionCollection($this->getTargetDefinitionStub());
        $this->assertInstanceOf("\\ArrayIterator", $definitionCollection->getIterator());
    }

    /**
     * @covers Brickoo\Component\Annotation\DefinitionCollection::isEmpty
     * @covers Brickoo\Component\Annotation\DefinitionCollection::count
     */
    public function testIsEmptyCollectionAndCountItems() {
        $annotation = $this->getAnnotationDefinitionStub();
        $definitionCollection = new DefinitionCollection($this->getTargetDefinitionStub());
        $this->assertTrue($definitionCollection->isEmpty());
        $this->assertEquals(0, count($definitionCollection));
        $definitionCollection->push($annotation);
        $this->assertFalse($definitionCollection->isEmpty());
        $this->assertEquals(1, count($definitionCollection));

    }

    /**
     * Return a TargetDefinition stub.
     * @return \Brickoo\Component\Annotation\Definition\TargetDefinition
     */
    private function getTargetDefinitionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\Definition\\TargetDefinition")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Return an AnnotationDefinition stub.
     * @return \Brickoo\Component\Annotation\Definition\AnnotationDefinition
     */
    private function getAnnotationDefinitionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\Definition\\AnnotationDefinition")
            ->disableOriginalConstructor()
            ->getMock();
    }

}