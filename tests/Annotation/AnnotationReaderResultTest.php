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
