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
