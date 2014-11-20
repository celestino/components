<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

use Brickoo\Component\IoC\Definition\InjectionDefinition;
use PHPUnit_Framework_TestCase;

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
