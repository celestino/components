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

use Brickoo\Component\Annotation\Annotation,
    Brickoo\Component\Annotation\Definition\AnnotationDefinition,
    PHPUnit_Framework_TestCase;

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
     * Returns a ParameterDefinition stub.
     * @return \Brickoo\Component\Annotation\Definition\ParameterDefinition
     */
    private function getDefinitionParameterStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\Definition\\ParameterDefinition")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
