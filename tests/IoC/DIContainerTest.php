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

namespace Brickoo\Tests\Component\IoC;

use Brickoo\Component\IoC\DIContainer;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Resolver\DefinitionResolver;
use PHPUnit_Framework_TestCase;

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
     * @covers Brickoo\Component\IoC\DIContainer::checkDependencyAccess
     * @covers Brickoo\Component\IoC\DIContainer::hasSingletonScope
     * @covers Brickoo\Component\IoC\DIContainer::createDependency
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
     * @covers Brickoo\Component\IoC\DIContainer::checkDependencyAccess
     * @covers Brickoo\Component\IoC\DIContainer::hasSingletonScope
     * @covers Brickoo\Component\IoC\DIContainer::createDependency
     * @covers Brickoo\Component\IoC\DIContainer::contains
     * @covers Brickoo\Component\IoC\DIContainer::resolveDefinition
     * @covers Brickoo\Component\IoC\DIContainer::getResolver
     * @covers Brickoo\Component\IoC\DIContainer::getSingleton
     * @covers Brickoo\Component\IoC\DIContainer::storeSingleton
     */
    public function testRetrieveDependencyWithSingletonScopeTwiceSameInstance() {
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
     * @covers Brickoo\Component\IoC\DIContainer::checkDependencyAccess
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
     * @covers Brickoo\Component\IoC\DIContainer::checkDependencyAccess
     * @covers Brickoo\Component\IoC\DIContainer::contains
     * @covers Brickoo\Component\IoC\Exception\InfiniteDependencyResolveLoopException
     * @expectedException \Brickoo\Component\IoC\Exception\InfiniteDependencyResolveLoopException
     */
    public function testRetrieveThrowsInfiniteDependencyResolveLoopException() {
        $definition = new DependencyDefinition(function(DIContainer $diContainer) {
            $diContainer->retrieve("dep");
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
