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

use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Resolver\DependencyCallableResolver;
use PHPUnit_Framework_TestCase;

/**
 * DependencyCallableResolverTest
 *
 * Test suite for the DependencyCallableResolver class.
 * @see Brickoo\Component\IoC\Resolver\DependencyCallableResolver
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyCallableResolverTest extends PHPUnit_Framework_TestCase {

    /** @var \stdClass */
    private $stdClass;

    /** @covers Brickoo\Component\IoC\Resolver\DependencyCallableResolver::resolve */
    public function testResolveDefinition() {
        $definition = new DependencyDefinition([$this, "getStdClass"]);

        $resolver = new DependencyCallableResolver($this->getDiContainerStub());
        $this->assertSame($this->getStdClass(), $resolver->resolve($definition));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyCallableResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyTypeException
     * @expectedException \Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyTypeException
     */
    public function testDependencyWithInvalidTypeThrowsException() {
        $resolver = new DependencyCallableResolver($this->getDiContainerStub());
        $resolver->resolve(new DependencyDefinition(["wrongType"]));
    }

    /**
     * @covers Brickoo\Component\IoC\Resolver\DependencyCallableResolver::resolve
     * @covers Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyResolverResultTypeException
     * @expectedException \Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyResolverResultTypeException
     */
    public function testDependencyReturnsInvalidResultTypeThrowsException() {
        $resolver = new DependencyCallableResolver($this->getDiContainerStub());
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
