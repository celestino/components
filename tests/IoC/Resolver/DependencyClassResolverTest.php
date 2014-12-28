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

use Brickoo\Component\IoC\Definition\ArgumentDefinition;
use Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer;
use Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Definition\InjectionDefinition;
use Brickoo\Component\IoC\Resolver\DependencyClassResolver;
use PHPUnit_Framework_TestCase;

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
