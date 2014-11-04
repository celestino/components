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

use Brickoo\Component\IoC\Definition\ArgumentDefinition;
use Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer;
use Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Definition\InjectionDefinition;
use Brickoo\Component\IoC\Resolver\DependencyResolver;
use PHPUnit_Framework_TestCase;

/**
 * DependencyResolverTest
 *
 * Test suite for the DependencyResolver class.
 * @see Brickoo\Component\IoC\Resolver\DependencyResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyResolverTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::__construct
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::getDiContainer
     */
    public function testGetDIContainer() {
        $diContainer = $this->getDiContainerStub();
        $resolver = new DependencyResolverFixture($diContainer);
        $this->assertSame($diContainer, $resolver->getDiContainer());
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::__construct
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::collectArguments
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::getArgumentIndex
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::getArgumentValue
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::injectDependencies
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::injectDependenciesToProperties
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::injectDependenciesToMethods
     */
    public function testResolveDefinition() {
        $definition = new DependencyDefinition(
            new \stdClass(),
            DependencyDefinition::SCOPE_PROTOTYPE,
            new ArgumentDefinitionContainer([
                new ArgumentDefinition("value", "name"),
                new ArgumentDefinition(function(){return "functionValue";}, "function"),
                new ArgumentDefinition("@dependency", "dependency")
            ]),
            new InjectionDefinitionContainer([
                new InjectionDefinition(InjectionDefinition::TARGET_METHOD, "injectDependency", new ArgumentDefinitionContainer([
                    new ArgumentDefinition("someDependency"),
                    new ArgumentDefinition(40)
                ])),
                new InjectionDefinition(InjectionDefinition::TARGET_PROPERTY, "injectedValue", new ArgumentDefinitionContainer([
                    new ArgumentDefinition("someValue")
                ]))
            ])
        );

        $container = $this->getDiContainerStub();
        $container->expects($this->any())
                  ->method("retrieve")
                  ->with("dependency")
                  ->will($this->returnValue("dependencyValue"));

        $resolver = new DependencyResolverFixture($container);
        $dependency = $resolver->resolve($definition);
        $this->assertEquals(["name" => "value", "function" => "functionValue", "dependency" => "dependencyValue"], $dependency->arguments);
        $this->assertEquals("someDependency", $dependency->injectedDependency);
        $this->assertEquals(40, $dependency->injectedNumber);
        $this->assertEquals("someValue", $dependency->injectedValue);
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::__construct
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::collectArguments
     * @covers Brickoo\Component\IoC\Resolver\DependencyResolver::injectDependencies
     */
    public function testResolveDefinitionWithEmptyContainers() {
        $resolver = new DependencyResolverFixture($this->getDiContainerStub());
        $dependency = $resolver->resolve(new DependencyDefinition(new \stdClass()));
        $this->assertTrue($dependency instanceof Dependency);
    }

    /**
     * Returns a DIContainer stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDiContainerStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\IoC\\DIContainer")
            ->disableOriginalConstructor()->getMock();
    }

}

class DependencyResolverFixture extends DependencyResolver {

    /** {@inheritdoc} */
    public function resolve(DependencyDefinition $dependencyDefinition) {
        $dependency = new Dependency();
        $dependency->arguments = $this->collectArguments($dependencyDefinition->getArgumentsContainer());
        $this->injectDependencies($dependency, $dependencyDefinition);
        return $dependency;
    }
}

class Dependency {
    public $arguments;
    public $injectedValue;
    public $injectedNumber;
    public $injectedDependency;
    public function injectDependency($dependency, $number) {
        $this->injectedDependency = $dependency;
        $this->injectedNumber = $number;
    }
}
