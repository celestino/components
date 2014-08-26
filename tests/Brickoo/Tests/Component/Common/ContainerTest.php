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

namespace Brickoo\Tests\Component\Common;

use Brickoo\Component\Common\Container,
    Brickoo\Component\Validation\Constraint\IsInternalTypeConstraint,
    Brickoo\Component\Validation\Validator\ConstraintValidator,
    PHPUnit_Framework_TestCase;

/**
 * containerTest
 *
 * Test suite for the container class.
 * @see Brickoo\Component\Common\Container
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ContainerTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Common\Container::__construct
     * @covers Brickoo\Component\Common\Container::getIterator
     */
    public function testGetIterator() {
        $container = new Container();
        $this->assertInstanceOf("\\Iterator", $container->getIterator());

    }

    /** @covers Brickoo\Component\Common\Container::count */
    public function testCountContainerEntries() {
        $container = new Container([1, 2, 3]);
        $this->assertEquals(3, count($container));
    }

    /** @covers Brickoo\Component\Common\Container::get */
    public function testGetContainerEntry() {
        $container = new Container(["key" => "value"]);
        $this->assertEquals("value", $container->get("key"));
        $this->assertEquals("DEFAULT", $container->get("undefined", "DEFAULT"));
    }

    /**
     * @covers Brickoo\Component\Common\Container::set
     * @covers Brickoo\Component\Common\Container::isValueTypeValid
     */
    public function testSetContainerEntry() {
        $container = new Container();
        $this->assertSame($container, $container->set("unit", "test"));
        $this->assertAttributeEquals(["unit" => "test"], "container", $container);
    }

    /**
     * @covers Brickoo\Component\Common\Container::set
     * @covers Brickoo\Component\Common\Container::isValueTypeValid
     * @covers Brickoo\Component\Common\Exception\InvalidValueTypeException
     * @expectedException \Brickoo\Component\Common\Exception\InvalidValueTypeException
     */
    public function testSetInvalidValueTypeThrowsException() {
        $container = new Container([], new ConstraintValidator(new IsInternalTypeConstraint("integer")));
        $container->set("unit", "test");
    }

    /** @covers Brickoo\Component\Common\Container::contains */
    public function testHasContainerEntry() {
        $container = new Container(["key" => "value"]);
        $this->assertTrue($container->contains("key"));
        $this->assertFalse($container->contains("undefined"));
    }

    /** @covers Brickoo\Component\Common\Container::remove */
    public function testRemoveContainerEntry() {
        $container = new Container(["key" => "value"]);
        $this->assertSame($container, $container->remove("key"));
        $this->assertAttributeEquals([], "container", $container);
    }

    /**
     * @covers Brickoo\Component\Common\Container::fromArray
     * @covers Brickoo\Component\Common\Container::isValueTypeValid
     */
    public function testFromArrayImport() {
        $expected  = ["test", "import"];
        $container = new Container();
        $this->assertSame($container, $container->fromArray($expected));
        $this->assertAttributeEquals($expected, "container", $container);
    }

    /**
     * @covers Brickoo\Component\Common\Container::fromArray
     * @covers Brickoo\Component\Common\Container::isValueTypeValid
     * @covers Brickoo\Component\Common\Exception\InvalidValueTypeException
     * @expectedException \Brickoo\Component\Common\Exception\InvalidValueTypeException
     */
    public function testFromArrayWithInvalidValueTypeThrowsException() {
        $container = new Container([], new ConstraintValidator(new IsInternalTypeConstraint("integer")));
        $container->fromArray([123, "wrongType"]);
    }

    /** @covers Brickoo\Component\Common\Container::toArray */
    public function testToArrayExport() {
        $expected = ["test"];
        $container = new Container();
        $this->assertSame($container, $container->fromArray($expected));
        $this->assertEquals($expected, $container->toArray());
    }

    /** @covers Brickoo\Component\Common\Container::isEmpty */
    public function testIsEmptyContainer() {
        $container = new Container();
        $this->assertTrue($container->isEmpty());
        $container->set("key", "value");
        $this->assertFalse($container->isEmpty());
    }

    /** @covers Brickoo\Component\Common\Container::clear */
    public function testFlushContainerEntries() {
        $container = new Container(["key" => "value"]);
        $container->clear();
        $this->assertAttributeEquals([], "container", $container);
    }

}
