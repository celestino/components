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

namespace Brickoo\Tests\Component\IoC\Resolver;

use Brickoo\Component\IoC\Definition\DependencyDefinition,
    Brickoo\Component\IoC\Resolver\DefinitionResolver,
    Brickoo\Component\IoC\Resolver\DependencyClassResolver,
    PHPUnit_Framework_TestCase;

/**
 * DefinitionResolverTest
 *
 * Test suite for the DefinitionResolver class.
 * @see Brickoo\Component\IoC\Resolver\DefinitionResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DefinitionResolverTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::__construct
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::setResolver
     */
    public function testSetResolver() {
        $resolver = new DependencyClassResolver($this->getDiContainerStub());
        $definitionResolver = new DefinitionResolver();
        $this->assertSame(
            $definitionResolver,
            $definitionResolver->setResolver(DefinitionResolver::TYPE_CLASS, $resolver)
        );
        $this->assertAttributeCount(1, "resolvers", $definitionResolver);
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolver
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolverType
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getDefinitionResolverByType
     */
    public function testResolverClassDefinitionWithResolverFromCache() {
        $definition = new DependencyDefinition("\\stdClass");
        $definitionResolver = new DefinitionResolver();
        $this->assertInstanceOf("\\stdClass", $definitionResolver->resolve(
            $this->getDiContainerStub(), $definition
        ));
        $this->assertInstanceOf("\\stdClass", $definitionResolver->resolve(
            $this->getDiContainerStub(), $definition
        ));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolver
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolverType
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getDefinitionResolverByType
     */
    public function testResolverObjectDefinition() {
        $dependency = new \stdClass();
        $definition = new DependencyDefinition($dependency);
        $definitionResolver = new DefinitionResolver();
        $this->assertSame($dependency, $definitionResolver->resolve(
            $this->getDiContainerStub(), $definition
        ));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolver
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolverType
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getDefinitionResolverByType
     */
    public function testResolverCallableDefinition() {
        $definition = new DependencyDefinition([$this, "callableCallbackResolverHelper"]);
        $definitionResolver = new DefinitionResolver();
        $this->assertInstanceOf("\\stdClass", $definitionResolver->resolve(
            $this->getDiContainerStub(), $definition
        ));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolver
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolverType
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getDefinitionResolverByType
     */
    public function testResolverClosureDefinition() {
        $dependency = new \stdClass();
        $definition = new DependencyDefinition(function($diContainer) use ($dependency){
            return $dependency;
        });
        $definitionResolver = new DefinitionResolver();
        $this->assertSame($dependency, $definitionResolver->resolve(
            $this->getDiContainerStub(), $definition
        ));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolver
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getResolverType
     * @covers Brickoo\Component\IoC\Resolver\DefinitionResolver::getDefinitionResolverByType
     * @covers Brickoo\Component\IoC\Resolver\Exception\DefinitionTypeUnknownException
     * @expectedException \Brickoo\Component\IoC\Resolver\Exception\DefinitionTypeUnknownException
     */
    public function testResolverUnsupportedDefinitionThrowsException() {
        $definition = new DependencyDefinition("\\DoesNotExist");
        $definitionResolver = new DefinitionResolver();
        $definitionResolver->resolve($this->getDiContainerStub(), $definition);
    }

    /**
     * Provides a callable test helper.
     * @return \stdClass
     */
    public function callableCallbackResolverHelper() {
        return new \stdClass();
    }

    /**
     * Returns a DIContainer stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDiContainerStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\IoC\\DIContainer")
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Returns a DependencyDefinition stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDependencyDefinitionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\IoC\\Definition\\DependencyDefinition")
            ->disableOriginalConstructor()->getMock();
    }

}

