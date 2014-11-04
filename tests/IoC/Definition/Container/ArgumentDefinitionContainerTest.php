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

namespace Brickoo\Tests\Component\IoC\Definition\Container;

use Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer;
use PHPUnit_Framework_TestCase;

/**
 * ArgumentDefinitionContainerTest
 *
 * Test suite for the ArgumentDefinitionContainer class.
 * @see Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ArgumentDefinitionContainerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer::__construct
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::isEmpty
     */
    public function testDefinitionContainerIsEmpty() {
        $definitionContainer = new ArgumentDefinitionContainer();
        $this->assertTrue($definitionContainer->isEmpty());
        $definitionContainer = new ArgumentDefinitionContainer([$this->getArgumentDefinitionStub()]);
        $this->assertFalse($definitionContainer->isEmpty());
    }

    /** @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::contains */
    public function testDefinitionContainerContainsAnArgument() {
        $argumentName = "argumentName";

        $argument = $this->getArgumentDefinitionStub();
        $argument->expects($this->any())
                 ->method("hasName")
                 ->will($this->returnValue(true));
        $argument->expects($this->any())
                 ->method("getName")
                 ->will($this->returnValue($argumentName));

        $definitionContainer = new ArgumentDefinitionContainer([$argument]);
        $this->assertTrue($definitionContainer->contains($argumentName));
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer::setArguments
     * @covers Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer::addArgument
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::add
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::count
     */
    public function testDefinitionContainerAddingListOfArguments() {
        $argumentList = [$this->getArgumentDefinitionStub(), $this->getArgumentDefinitionStub()];
        $definitionContainer = new ArgumentDefinitionContainer();
        $this->assertSame($definitionContainer, $definitionContainer->setArguments($argumentList));
        $this->assertEquals(2, count($definitionContainer));
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer::addArgument
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::add
     * @covers Brickoo\Component\IoC\Definition\Container\Exception\DuplicateParameterDefinitionException
     * @expectedException \Brickoo\Component\IoC\Definition\Container\Exception\DuplicateParameterDefinitionException
     */
    public function testDefinitionContainerAddingDuplicateArgumentThrowsException() {
        $argument = $this->getArgumentDefinitionStub();
        $argument->expects($this->any())
                 ->method("hasName")
                 ->will($this->returnValue(true));
        $argument->expects($this->any())
                 ->method("getName")
                 ->will($this->returnValue("argumentName"));

        $definitionContainer = new ArgumentDefinitionContainer([$argument]);
        $definitionContainer->addArgument($argument);
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer::setArguments
     * @expectedException \InvalidArgumentException
     */
    public function testDefinitionContainerSetInvalidTypeThrowsException() {
        $definitionContainer = new ArgumentDefinitionContainer();
        $definitionContainer->setArguments([new \stdClass()]);
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::remove
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::count
     */
    public function testDefinitionContainerRemovingArgument() {
        $argument = $this->getArgumentDefinitionStub();
        $argument->expects($this->any())
                 ->method("hasName")
                 ->will($this->returnValue(true));
        $argument->expects($this->any())
                 ->method("getName")
                 ->will($this->returnValue("argumentName"));

        $definitionContainer = new ArgumentDefinitionContainer([$argument]);
        $this->assertEquals(1, count($definitionContainer));
        $this->assertSame($definitionContainer, $definitionContainer->remove("argumentName"));
        $this->assertEquals(0, count($definitionContainer));
    }

    /** @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::get */
    public function testDefinitionContainerRetrievingArgument() {
        $argument = $this->getArgumentDefinitionStub();
        $argument->expects($this->any())
                 ->method("hasName")
                 ->will($this->returnValue(true));
        $argument->expects($this->any())
                 ->method("getName")
                 ->will($this->returnValue("argumentName"));

        $definitionContainer = new ArgumentDefinitionContainer([$argument]);
        $this->assertSame($argument, $definitionContainer->get("argumentName"));
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::get
     * @covers Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException
     * @expectedException \Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException
     */
    public function testDefinitionContainerRetrievingUnknownArgumentThrowsException() {
        $definitionContainer = new ArgumentDefinitionContainer();
        $definitionContainer->get("someArgumentName");
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::getIterator
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::getAll
     */
    public function testDefinitionContainerRetrievingAllArguments() {
        $definitionContainer = new ArgumentDefinitionContainer([$this->getArgumentDefinitionStub()]);
        $argumentsIterator = $definitionContainer->getIterator();
        $this->assertEquals(1, $argumentsIterator->count());
    }

    /**
     * Returns an ArgumentDefinition stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getArgumentDefinitionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\IoC\\Definition\\ArgumentDefinition")
            ->disableOriginalConstructor()->getMock();
    }

}
