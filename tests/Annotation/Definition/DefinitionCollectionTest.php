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

namespace Brickoo\Tests\Component\Annotation\Definition;

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\Definition\DefinitionCollection;
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the DefinitionCollection class.
 * @see Brickoo\Component\Annotation\Definition\DefinitionCollection
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class DefinitionCollectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::__construct
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::getName
     */
    public function testGetName() {
        $definitionName = "uniqueName";
        $definitionCollection = new DefinitionCollection($definitionName);
        $this->assertEquals($definitionName, $definitionCollection->getName());
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::push
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::shift
     */
    public function testPushAndShiftAnnotationDefinitionFromCollection() {
        $annotation = $this->getAnnotationDefinitionStub();
        $definitionCollection = new DefinitionCollection("uniqueName");
        $this->assertSame($definitionCollection, $definitionCollection->push($annotation));
        $this->assertSame($definitionCollection, $definitionCollection->push(clone $annotation));
        $this->assertSame($annotation, $definitionCollection->shift());
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::shift
     * @covers Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @expectedException \Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     */
    public function testShiftWithEmptyCollectionThrowsException() {
        $definitionCollection = new DefinitionCollection("uniqueName");
        $definitionCollection->shift();
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::push
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::pop
     */
    public function testPushAndPopAnnotationDefinitionFromCollection() {
        $annotation = $this->getAnnotationDefinitionStub();
        $definitionCollection = new DefinitionCollection("uniqueName");
        $this->assertSame($definitionCollection, $definitionCollection->push(clone $annotation));
        $this->assertSame($definitionCollection, $definitionCollection->push($annotation));
        $this->assertSame($annotation, $definitionCollection->pop());
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::pop
     * @covers Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @expectedException \Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     */
    public function testPopWithEmptyCollectionThrowsException() {
        $definitionCollection = new DefinitionCollection("uniqueName");
        $definitionCollection->pop();
    }

    /** @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::getIterator */
    public function testGetCollectionArrayIterator() {
        $definitionCollection = new DefinitionCollection("uniqueName");
        $this->assertInstanceOf("\\ArrayIterator", $definitionCollection->getIterator());
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::isEmpty
     * @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::count
     */
    public function testIsEmptyCollectionAndCountItems() {
        $annotation = $this->getAnnotationDefinitionStub();
        $definitionCollection = new DefinitionCollection("uniqueName");
        $this->assertTrue($definitionCollection->isEmpty());
        $this->assertEquals(0, count($definitionCollection));
        $definitionCollection->push($annotation);
        $this->assertFalse($definitionCollection->isEmpty());
        $this->assertEquals(1, count($definitionCollection));

    }

    /** @covers Brickoo\Component\Annotation\Definition\DefinitionCollection::getAnnotationsDefinitionsByTarget */
    public function testGetAnnotationsDefinitionsByTarget() {
        $annotationA = $this->getAnnotationDefinitionStub();
        $annotationA->expects($this->any())
                    ->method("isTarget")
                    ->with(Annotation::TARGET_METHOD)
                    ->will($this->returnValue(false));
        $annotationB = $this->getAnnotationDefinitionStub();
        $annotationB->expects($this->any())
                    ->method("isTarget")
                    ->with(Annotation::TARGET_METHOD)
                    ->will($this->returnValue(true));
        $definitionCollection = new DefinitionCollection("uniqueName");
        $definitionCollection->push($annotationA);
        $definitionCollection->push($annotationB);
        $this->assertInstanceOf(
            "\\ArrayIterator",
            ($iterator = $definitionCollection->getAnnotationsDefinitionsByTarget(Annotation::TARGET_METHOD))
        );
        $this->assertSame($annotationB, $iterator->current());
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
