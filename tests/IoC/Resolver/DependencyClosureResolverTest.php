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
use Brickoo\Component\IoC\Resolver\DependencyClosureResolver;
use PHPUnit_Framework_TestCase;

/**
 * DependencyClosureResolverTest
 *
 * Test suite for the DependencyClosureResolver class.
 * @see Brickoo\Component\IoC\Resolver\DependencyClosureResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyClosureResolverTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\IoC\Resolver\DependencyClosureResolver::resolve */
    public function testResolveDefinition() {
        $dependency = new \stdClass();
        $definition = new DependencyDefinition(function() use ($dependency){return $dependency;});

        $resolver = new DependencyClosureResolver($this->getDiContainerStub());
        $this->assertSame($dependency, $resolver->resolve($definition));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyClosureResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyTypeException
     * @expectedException \Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyTypeException
     */
    public function testDependencyWithInvalidTypeThrowsException() {
        $resolver = new DependencyClosureResolver($this->getDiContainerStub());
        $resolver->resolve(new DependencyDefinition(["wrongType"]));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyClosureResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyResolverResultTypeException
     * @expectedException \Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyResolverResultTypeException
     */
    public function testDependencyReturnsInvalidResultTypeThrowsException() {
        $resolver = new DependencyClosureResolver($this->getDiContainerStub());
        $resolver->resolve(new DependencyDefinition(function(){return "notAnObject";}));
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
     * Method for testing callable function  calls.
     * @return \stdClass
     */
    public function getStdClass() {
        if ($this->stdClass === null) {
            $this->stdClass = new \stdClass();
        }
        return $this->stdClass;
    }

}
