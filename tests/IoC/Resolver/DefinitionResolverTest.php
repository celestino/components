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

namespace Brickoo\Tests\Component\IoC\Resolver;

use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Resolver\DefinitionResolver;
use Brickoo\Component\IoC\Resolver\DependencyClassResolver;
use PHPUnit_Framework_TestCase;

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

