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

namespace Brickoo\Tests\Component\IoC;

use Brickoo\Component\IoC\DIContainer,
    Brickoo\Component\IoC\Definition\DependencyDefinition,
    Brickoo\Component\IoC\Resolver\DefinitionResolver,
    PHPUnit_Framework_TestCase;

/**
 * DIContainerTest
 *
 * Test suite for the DIContainer class.
 * @see Brickoo\Component\IoC\DIContainer
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DIContainerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\DIContainer::__construct
     * @covers Brickoo\Component\IoC\DIContainer::set
     */
    public function testConstructorSetDefinitions() {
        $container = new DIContainer($this->getDefinitionResolverStub(), ["dep" => $this->getDependencyDefinitionStub()]);
        $this->assertAttributeCount(1, "container", $container);
    }

    /** @covers Brickoo\Component\IoC\DIContainer::getResolver */
    public function testGetDefinitionResolver() {
        $resolver = $this->getDefinitionResolverStub();
        $container = new DIContainer($resolver);
        $this->assertSame($resolver, $container->getResolver());
    }

    /**
     * @covers Brickoo\Component\IoC\DIContainer::retrieve
     * @covers Brickoo\Component\IoC\DIContainer::contains
     * @covers Brickoo\Component\IoC\DIContainer::resolveDefinition
     * @covers Brickoo\Component\IoC\DIContainer::getResolver
     */
    public function testRetrieveDependencyWithPrototypeScope() {
        $dependency = new \stdClass();
        $definition = $this->getDependencyDefinitionStub();
        $definition->expects($this->any())
                   ->method("getScope")
                   ->will($this->returnValue(DependencyDefinition::SCOPE_PROTOTYPE));

        $resolver = $this->getDefinitionResolverStub();
        $resolver->expects($this->any())
                 ->method("resolve")
                 ->will($this->returnValue($dependency));

        $container = new DIContainer($resolver, ["dep" => $definition]);
        $this->assertSame($dependency, $container->retrieve("dep"));
    }

    /**
     * @covers Brickoo\Component\IoC\DIContainer::retrieve
     * @covers Brickoo\Component\IoC\DIContainer::contains
     * @covers Brickoo\Component\IoC\DIContainer::resolveDefinition
     * @covers Brickoo\Component\IoC\DIContainer::getResolver
     * @covers Brickoo\Component\IoC\DIContainer::getSingleton
     * @covers Brickoo\Component\IoC\DIContainer::storeSingleton
     */
    public function testRetrieveDependencyWithSingletonScope() {
        $dependency = new \stdClass();
        $definition = $this->getDependencyDefinitionStub();
        $definition->expects($this->any())
                   ->method("getScope")
                   ->will($this->returnValue(DependencyDefinition::SCOPE_SINGLETON));

        $resolver = $this->getDefinitionResolverStub();
        $resolver->expects($this->once())
            ->method("resolve")
            ->will($this->returnValue($dependency));

        $container = new DIContainer($resolver, ["dep" => $definition]);
        $this->assertSame($dependency, $container->retrieve("dep"));
        $this->assertSame($dependency, $container->retrieve("dep"));
    }

    /**
     * @covers Brickoo\Component\IoC\DIContainer::retrieve
     * @covers Brickoo\Component\IoC\DIContainer::contains
     * @covers Brickoo\Component\IoC\Exception\DefinitionNotAvailableException
     * @expectedException \Brickoo\Component\IoC\Exception\DefinitionNotAvailableException
     */
    public function testRetrieveThrowsDependencyNotAvailableException() {
        $container = new DIContainer($this->getDefinitionResolverStub());
        $container->retrieve("dep");
    }

    /**
     * @covers Brickoo\Component\IoC\DIContainer::retrieve
     * @covers Brickoo\Component\IoC\DIContainer::contains
     * @covers Brickoo\Component\IoC\Exception\InfiniteDependencyResolveLoopException
     * @expectedException \Brickoo\Component\IoC\Exception\InfiniteDependencyResolveLoopException
     */
    public function testRetrieveThrowsInfiniteDependencyResolveLoopException() {
        $definition = new DependencyDefinition(function() {
            $this->retrieve("dep");
        });
        $container = new DIContainer(new DefinitionResolver(), ["dep" => $definition]);
        $container->retrieve("dep");
    }

    /**
     * Returns a DefinitionResolver stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDefinitionResolverStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\IoC\\Resolver\\DefinitionResolver")
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
