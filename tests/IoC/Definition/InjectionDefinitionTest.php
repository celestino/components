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

namespace Brickoo\Tests\Component\IoC\Definition;

use Brickoo\Component\IoC\Definition\InjectionDefinition,
    PHPUnit_Framework_TestCase;

/**
 * InjectionDefinitionTest
 *
 * Test suite for the InjectionDefinition class.
 * @see Brickoo\Component\IoC\Definition\InjectionDefinition
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InjectionDefinitionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\Definition\InjectionDefinition::__construct
     * @covers Brickoo\Component\IoC\Definition\InjectionDefinition::getTarget
     * @covers Brickoo\Component\IoC\Definition\InjectionDefinition::isTarget
     * @covers Brickoo\Component\IoC\Definition\InjectionDefinition::getTargetName
     * @covers Brickoo\Component\IoC\Definition\InjectionDefinition::getArgumentsContainer
     */
    public function testDefinitionImplementation() {
        $target = InjectionDefinition::TARGET_METHOD;
        $targetName = "\\SomeClass::targetMethodName";
        $container = $this->getMock("\\Brickoo\\Component\\IoC\\Definition\\Container\\ArgumentDefinitionContainer");

        $injectionDefinition = new InjectionDefinition($target, $targetName, $container);
        $this->assertEquals($target, $injectionDefinition->getTarget());
        $this->assertEquals($targetName, $injectionDefinition->getTargetName());
        $this->assertSame($container, $injectionDefinition->getArgumentsContainer());

        $this->assertFalse($injectionDefinition->isTarget(InjectionDefinition::TARGET_CONSTRUCTOR));
        $this->assertTrue($injectionDefinition->isTarget(InjectionDefinition::TARGET_METHOD));
    }

}
