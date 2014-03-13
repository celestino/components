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

use Brickoo\Component\Annotation\Annotation,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the Annotation class.
 * @see Brickoo\Component\Annotation\Annotation
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AutoloaderTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Annotation\Annotation::__construct */
    public function testConstructor() {
        $annotation =  new Annotation("Cache", ["path" => "/"]);
        $this->assertAttributeEquals("Cache", "name", $annotation);
        $this->assertAttributeEquals(["path" => "/"], "values", $annotation);
    }

    /**
     * @covers Brickoo\Component\Annotation\Annotation::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsInvalidNameArgumentException() {
        new Annotation(["wrongType"]);
    }

    /** @covers Brickoo\Component\Annotation\Annotation::getName */
    public function testGetName() {
        $annotationName = "Cache";
        $annotation =  new Annotation($annotationName);
        $this->assertEquals($annotationName, $annotation->getName());
    }

    /** @covers Brickoo\Component\Annotation\Annotation::getValues */
    public function testGetValues() {
        $annotationValues = ["path" => "/"];
        $annotation =  new Annotation("Cache", $annotationValues);
        $this->assertEquals($annotationValues, $annotation->getValues());
    }

    /** @covers Brickoo\Component\Annotation\Annotation::hasValues */
    public function testHasValues() {
        $annotationValues = ["path" => "/"];
        $annotation1 =  new Annotation("Cache");
        $this->assertFalse($annotation1->hasValues());
        $annotation2 =  new Annotation("Cache", $annotationValues);
        $this->assertTrue($annotation2->hasValues());
    }

}