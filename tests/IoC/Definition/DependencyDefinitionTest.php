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

use Brickoo\Component\IoC\Definition\DependencyDefinition;
use PHPUnit_Framework_TestCase;

/**
 * DependencyDefinitionTest
 *
 * Test suite for the DependencyDefinition class.
 * @see Brickoo\Component\IoC\Definition\DependencyDefinition
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyDefinitionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\Definition\DependencyDefinition::__construct
     * @covers Brickoo\Component\IoC\Definition\DependencyDefinition::getDependency
     * @covers Brickoo\Component\IoC\Definition\DependencyDefinition::setDependency
     */
    public function testDefinitionScope() {
        $dependency = "@MyDependency";
        $annotationDefinition = new DependencyDefinition($dependency);
        $this->assertEquals($dependency, $annotationDefinition->getDependency());
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\DependencyDefinition::getScope
     * @covers Brickoo\Component\IoC\Definition\DependencyDefinition::getArgumentsContainer
     * @covers Brickoo\Component\IoC\Definition\DependencyDefinition::getInjectionsContainer
     */
    public function testDefinitionGetterMethods() {
        $scope = DependencyDefinition::SCOPE_SINGLETON;
        $argumentContainer = $this->getMock("\\Brickoo\\Component\\IoC\\Definition\\Container\\ArgumentDefinitionContainer");
        $injectionContainer = $this->getMock("\\Brickoo\\Component\\IoC\\Definition\\Container\\InjectionDefinitionContainer");

        $annotationDefinition = new DependencyDefinition("@MyDependency", $scope, $argumentContainer, $injectionContainer);
        $this->assertEquals($scope, $annotationDefinition->getScope());
        $this->assertSame($argumentContainer, $annotationDefinition->getArgumentsContainer());
        $this->assertSame($injectionContainer, $annotationDefinition->getInjectionsContainer());
    }

}
