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

use Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer;
use Brickoo\Component\IoC\Definition\InjectionDefinition;
use PHPUnit_Framework_TestCase;

/**
 * InjectionDefinitionContainerTest
 *
 * Test suite for the InjectionDefinitionContainer class.
 * @see Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InjectionDefinitionContainerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer::__construct
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::isEmpty
     */
    public function testDefinitionContainerIsEmpty() {
        $definitionContainer = new InjectionDefinitionContainer();
        $this->assertTrue($definitionContainer->isEmpty());

        $injection = $this->getInjectionDefinitionStub();
        $injection->expects($this->any())
                  ->method("getTargetName")
                  ->will($this->returnValue("\\SomeClass::someMethod"));
        $definitionContainer = new InjectionDefinitionContainer([$injection]);
        $this->assertFalse($definitionContainer->isEmpty());
    }

    /** @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::contains */
    public function testDefinitionContainerContainsAnInjectionDefinition() {
        $injectionName = "\\SomeClass::someMethod";

        $injection = $this->getInjectionDefinitionStub();
        $injection->expects($this->any())
                  ->method("getTargetName")
                  ->will($this->returnValue($injectionName));

        $definitionContainer = new InjectionDefinitionContainer([$injection]);
        $this->assertTrue($definitionContainer->contains($injectionName));
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer::setInjections
     * @covers Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer::addInjection
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::add
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::count
     */
    public function testDefinitionContainerAddingListOfInjections() {
        $injection_1 = $this->getInjectionDefinitionStub();
        $injection_1->expects($this->any())
                    ->method("getTargetName")
                    ->will($this->returnValue("\\SomeClass::someMethod_1"));

        $injection_2 = $this->getInjectionDefinitionStub();
        $injection_2->expects($this->any())
                    ->method("getTargetName")
                    ->will($this->returnValue("\\SomeClass::someMethod_2"));

        $injectionList = [$injection_1, $injection_2];
        $definitionContainer = new InjectionDefinitionContainer();
        $this->assertSame($definitionContainer, $definitionContainer->setInjections($injectionList));
        $this->assertEquals(2, count($definitionContainer));
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer::addInjection
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::add
     * @covers Brickoo\Component\IoC\Definition\Container\Exception\DuplicateInjectionDefinitionException
     * @expectedException \Brickoo\Component\IoC\Definition\Container\Exception\DuplicateInjectionDefinitionException
     */
    public function testDefinitionContainerAddingDuplicateArgumentThrowsException() {
        $injection = $this->getInjectionDefinitionStub();
        $injection->expects($this->any())
                  ->method("getTargetName")
                  ->will($this->returnValue("\\SomeClass::someMethod"));

        $definitionContainer = new InjectionDefinitionContainer([$injection]);
        $definitionContainer->addInjection($injection);
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer::setInjections
     * @expectedException \InvalidArgumentException
     */
    public function testDefinitionContainerSetInvalidTypeThrowsException() {
        $definitionContainer = new InjectionDefinitionContainer();
        $definitionContainer->setInjections([new \stdClass()]);
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::remove
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::count
     */
    public function testDefinitionContainerRemovingArgument() {
        $injectionName = "\\SomeClass::someMethod";

        $injection = $this->getInjectionDefinitionStub();
        $injection->expects($this->any())
                  ->method("getTargetName")
                  ->will($this->returnValue($injectionName));

        $definitionContainer = new InjectionDefinitionContainer([$injection]);
        $this->assertEquals(1, count($definitionContainer));
        $this->assertSame($definitionContainer, $definitionContainer->remove($injectionName));
        $this->assertEquals(0, count($definitionContainer));
    }

    /** @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::get */
    public function testDefinitionContainerRetrievingArgument() {
        $injectionName = "\\SomeClass::someMethod";

        $injection = $this->getInjectionDefinitionStub();
        $injection->expects($this->any())
                  ->method("getTargetName")
                  ->will($this->returnValue($injectionName));

        $definitionContainer = new InjectionDefinitionContainer([$injection]);
        $this->assertSame($injection, $definitionContainer->get($injectionName));
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::get
     * @covers Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException
     * @expectedException \Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException
     */
    public function testDefinitionContainerRetrievingUnknownArgumentThrowsException() {
        $definitionContainer = new InjectionDefinitionContainer();
        $definitionContainer->get("injectionTargetName");
    }

    /**
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::getIterator
     * @covers Brickoo\Component\IoC\Definition\Container\DefinitionContainer::getAll
     */
    public function testDefinitionContainerRetrievingAllArguments() {
        $injectionName = "\\SomeClass::someMethod";

        $injection = $this->getInjectionDefinitionStub();
        $injection->expects($this->any())
                  ->method("getTargetName")
                  ->will($this->returnValue($injectionName));

        $definitionContainer = new InjectionDefinitionContainer([$injection]);
        $injectionsIterator = $definitionContainer->getIterator();
        $this->assertEquals(1, $injectionsIterator->count());
    }

    /** @covers Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer::getByTarget */
    public function testGetInjectionsByTarget() {
        $injection_1 = $this->getInjectionDefinitionStub();
        $injection_1->expects($this->any())
                    ->method("isTarget")
                    ->will($this->returnValue(false));
        $injection_1->expects($this->any())
                    ->method("getTargetName")
                    ->will($this->returnValue("\\SomeClass"));

        $injection_2 = $this->getInjectionDefinitionStub();
        $injection_2->expects($this->any())
                    ->method("isTarget")
                    ->will($this->returnValue(true));
        $injection_2->expects($this->any())
                    ->method("getTargetName")
                    ->will($this->returnValue("\\SomeClass::someMethod"));

        $definitionContainer = new InjectionDefinitionContainer([$injection_1, $injection_2]);
        $container = $definitionContainer->getByTarget(InjectionDefinition::TARGET_METHOD);
        $this->assertEquals(1, count($container));
        $this->assertSame($injection_2, array_shift($container));
    }

    /**
     * Returns an InjectionDefinition stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getInjectionDefinitionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\IoC\\Definition\\InjectionDefinition")
            ->disableOriginalConstructor()->getMock();
    }

}
