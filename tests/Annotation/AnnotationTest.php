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
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the Annotation class.
 * @see Brickoo\Component\Annotation\Annotation
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\Annotation::__construct
     * @covers Brickoo\Component\Annotation\Annotation::getTarget
     */
    public function testGetTarget() {
        $target = Annotation::TARGET_CLASS;
        $annotation =  new Annotation($target, "\\SomeClass", "Cache");
        $this->assertEquals($target, $annotation->getTarget());
    }

    /**
     * @covers Brickoo\Component\Annotation\Annotation::__construct
     * @covers Brickoo\Component\Annotation\Annotation::getTargetLocation
     */
    public function testGetTargetLocation() {
        $targetLocation = "\\SomeClass";
        $annotation =  new Annotation(Annotation::TARGET_CLASS, $targetLocation, "Cache");
        $this->assertEquals($targetLocation, $annotation->getTargetLocation());
    }

    /** @covers Brickoo\Component\Annotation\Annotation::getName */
    public function testGetName() {
        $annotationName = "Cache";
        $annotation =  new Annotation(Annotation::TARGET_CLASS, "\\SomeClass", $annotationName);
        $this->assertEquals($annotationName, $annotation->getName());
    }

    /** @covers Brickoo\Component\Annotation\Annotation::getValues */
    public function testGetValues() {
        $annotationValues = ["path" => "/"];
        $annotation =  new Annotation(Annotation::TARGET_CLASS, "\\SomeClass", "Cache", $annotationValues);
        $this->assertEquals($annotationValues, $annotation->getValues());
    }

    /** @covers Brickoo\Component\Annotation\Annotation::hasValues */
    public function testHasValues() {
        $annotationValues = ["path" => "/"];
        $annotation1 =  new Annotation(Annotation::TARGET_CLASS, "\\SomeClass", "Cache");
        $this->assertFalse($annotation1->hasValues());
        $annotation2 =  new Annotation(Annotation::TARGET_CLASS, "\\SomeClass", "Cache", $annotationValues);
        $this->assertTrue($annotation2->hasValues());
    }

}
