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
