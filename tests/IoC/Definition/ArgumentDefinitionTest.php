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

namespace Brickoo\Tests\Component\IoC\Definition;

use Brickoo\Component\IoC\Definition\ArgumentDefinition;
use PHPUnit_Framework_TestCase;

/**
 * ArgumentDefinitionTest
 *
 * Test suite for the ArgumentDefinition class.
 * @see Brickoo\Component\IoC\Definition\ArgumentDefinition
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ArgumentDefinitionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\Definition\ArgumentDefinition::__construct
     * @covers Brickoo\Component\IoC\Definition\ArgumentDefinition::getName
     * @covers Brickoo\Component\IoC\Definition\ArgumentDefinition::getValue
     */
    public function testDefinitionGetter() {
        $name = "MyAnnotation";
        $value = "some value";
        $annotationDefinition = new ArgumentDefinition($value, $name);
        $this->assertEquals($name, $annotationDefinition->getName());
        $this->assertEquals($value, $annotationDefinition->getValue());
    }

    /** @covers Brickoo\Component\IoC\Definition\ArgumentDefinition::hasName */
    public function testHasName() {
        $annotationDefinition = new ArgumentDefinition("some value");
        $this->assertFalse($annotationDefinition->hasName());
        $annotationDefinition = new ArgumentDefinition("some value", "MyAnnotation");
        $this->assertTrue($annotationDefinition->hasName());
    }

    /** @covers Brickoo\Component\IoC\Definition\ArgumentDefinition::setValue */
    public function testSetValue() {
        $newValue =" new value";
        $annotationDefinition = new ArgumentDefinition("some value");
        $this->assertSame($annotationDefinition, $annotationDefinition->setValue($newValue));
        $this->assertEquals($newValue, $annotationDefinition->getValue());
    }

}
