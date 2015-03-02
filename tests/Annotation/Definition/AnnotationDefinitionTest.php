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
use Brickoo\Component\Annotation\Definition\AnnotationDefinition;
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationDefinition class.
 * @see Brickoo\Component\Annotation\Definition\AnnotationDefinition
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationDefinitionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::__construct
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::getName
     */
    public function testGetName() {
        $name = "Controller";
        $definition = new AnnotationDefinition($name);
        $this->assertEquals($name, $definition->getName());
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::__construct
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::getTarget
     */
    public function testGetTarget() {
        $target = Annotation::TARGET_CLASS;
        $definition = new AnnotationDefinition("someName", $target);
        $this->assertEquals($target, $definition->getTarget());
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::__construct
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::isTarget
     */
    public function testIsTarget() {
        $target = Annotation::TARGET_CLASS;
        $definition = new AnnotationDefinition("someName", $target);
        $this->assertFalse($definition->isTarget(Annotation::TARGET_METHOD));
        $this->assertTrue($definition->isTarget($target));
    }

    /** @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::isRequired */
    public function testIsRequired() {
        $definitionA = new AnnotationDefinition("Controller");
        $this->assertTrue($definitionA->isRequired());
        $definitionB = new AnnotationDefinition("Controller", Annotation::TARGET_CLASS, false);
        $this->assertFalse($definitionB->isRequired());
    }

    /** @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::addParameter */
    public function testAddParameter() {
        $parameter = $this->getDefinitionParameterStub();
        $parameter->expects($this->any())
                  ->method("isRequired")
                  ->will($this->returnValue(true));
        $definition = new AnnotationDefinition("Controller");
        $this->assertSame($definition, $definition->addParameter($parameter));
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::addParameter
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::hasRequiredParameters
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::getRequiredParameters
     */
    public function testRequiredParameterRoutines() {
        $parameter = $this->getDefinitionParameterStub();
        $parameter->expects($this->any())
                  ->method("isRequired")
                  ->will($this->returnValue(true));
        $definition = new AnnotationDefinition("Controller");
        $this->assertSame($definition, $definition->addParameter($parameter));
        $this->assertTrue($definition->hasRequiredParameters());
        $this->assertEquals([$parameter], $definition->getRequiredParameters());
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::addParameter
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinition::getOptionalParameters
     */
    public function testOptionalParameterRoutines() {
        $parameter = $this->getDefinitionParameterStub();
        $parameter->expects($this->any())
                  ->method("isRequired")
                  ->will($this->returnValue(false));
        $definition = new AnnotationDefinition("Controller");
        $this->assertSame($definition, $definition->addParameter($parameter));
        $this->assertFalse($definition->hasRequiredParameters());
        $this->assertEquals([$parameter], $definition->getOptionalParameters());
    }

    /**
     * Returns a AnnotationParameterDefinition stub.
     * @return \Brickoo\Component\Annotation\Definition\AnnotationParameterDefinition
     */
    private function getDefinitionParameterStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\Definition\\AnnotationParameterDefinition")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
