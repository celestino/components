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

use Brickoo\Component\IoC\Definition\DependencyDefinition,
    PHPUnit_Framework_TestCase;

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
