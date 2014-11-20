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
