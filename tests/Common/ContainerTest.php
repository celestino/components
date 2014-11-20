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

namespace Brickoo\Tests\Component\Common;

use Brickoo\Component\Common\Container;
use Brickoo\Component\Validation\Constraint\IsInternalTypeConstraint;
use Brickoo\Component\Validation\Validator\ConstraintValidator;
use PHPUnit_Framework_TestCase;

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
