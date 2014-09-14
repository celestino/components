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

use Brickoo\Component\IoC\Definition\ArgumentDefinition,
    Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer,
    Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer,
    Brickoo\Component\IoC\Definition\DependencyDefinition,
    Brickoo\Component\IoC\Definition\InjectionDefinition,
    Brickoo\Component\IoC\Resolver\DependencyClassResolver,
    PHPUnit_Framework_TestCase;

/**
 * DependencyClassResolverTest
 *
 * Test suite for the DependencyClassResolver class.
 * @see Brickoo\Component\IoC\Resolver\DependencyClassResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyClassResolverTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\IoC\Resolver\DependencyClassResolver::resolve */
    public function testResolveDefinition() {
        $definition = new DependencyDefinition("\\stdClass");

        $resolver = new DependencyClassResolver($this->getDiContainerStub());
        $this->assertInstanceOf("\\stdClass", $resolver->resolve($definition));
    }

    /** @covers Brickoo\Component\IoC\Resolver\DependencyClassResolver::resolve */
    public function testResolveDefinitionWithConstructorArguments() {
        include __DIR__ . "/Assets/TestDependencyObject.php";

        $className = __NAMESPACE__."\\Assets\\TestDependencyObject";
        $definition = new DependencyDefinition(
            $className,
            DependencyDefinition::SCOPE_PROTOTYPE,
            null,
            new InjectionDefinitionContainer([
                new InjectionDefinition(InjectionDefinition::TARGET_CONSTRUCTOR, "__construct", new ArgumentDefinitionContainer([
                    new ArgumentDefinition("value")
                ]))
            ])
        );

        $resolver = new DependencyClassResolver($this->getDiContainerStub());
        $this->assertInstanceOf($className, ($dependency = $resolver->resolve($definition)));
        $this->assertEquals("value", $dependency->key);
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyClassResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\Exception\DependencyClassUnknownException
     * @expectedException \Brickoo\Component\IoC\Resolver\Exception\DependencyClassUnknownException
     */
    public function testDependencyWithUnknownClassThrowsException() {
        $resolver = new DependencyClassResolver($this->getDiContainerStub());
        $resolver->resolve(new DependencyDefinition("\\UnknownClass"));
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
