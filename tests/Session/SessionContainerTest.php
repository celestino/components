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

namespace Brickoo\Tests\Component\Session;

use Brickoo\Component\Session\SessionContainer;
use PHPUnit_Framework_TestCase;

/**
 * SessionContainerTest
 *
 * Test suite for the SessionContainer class.
 * @see Brickoo\Component\Session\SessionContainer
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SessionContainerTest extends PHPUnit_Framework_TestCase {

    /** @var \Brickoo\Component\Session\SessionContainer */
    protected $sessionContainer;

    /**
     * Set up the Container instance used.
     * Clean up the global $_SESSION variable.
     * @return void
     */
    public function setUp() {
        $_SESSION = array("my_namespace.test_property" => "some value");
        $this->sessionContainer = new SessionContainer("my_namespace");
    }

    /**
     * @covers Brickoo\Component\Session\SessionContainer::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructArgumentException() {
        new SessionContainer(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Session\SessionContainer::contains
     * @covers Brickoo\Component\Session\SessionContainer::getNamespace
     */
    public function testContains() {
        $this->assertTrue($this->sessionContainer->contains("test_property"));
        $this->assertFalse($this->sessionContainer->contains("not_available"));
    }

    /**
     * @covers Brickoo\Component\Session\SessionContainer::get
     * @covers Brickoo\Component\Session\SessionContainer::getNamespace
     */
    public function testGet() {
        $this->assertEquals("some value", $this->sessionContainer->get("test_property"));
        $this->assertEquals("default value", $this->sessionContainer->get("not_available", "default value"));
    }

    /**
     * @covers Brickoo\Component\Session\SessionContainer::set
     * @covers Brickoo\Component\Session\SessionContainer::getNamespace
     */
    public function testSet() {
        $this->assertSame($this->sessionContainer, $this->sessionContainer->set("new_property", "new value"));
        $this->assertTrue(($_SESSION["my_namespace.new_property"] == "new value"));
    }

    /**
     * @covers Brickoo\Component\Session\SessionContainer::remove
     * @covers Brickoo\Component\Session\SessionContainer::getNamespace
     */
    public function testRemove() {
        $this->assertSame($this->sessionContainer, $this->sessionContainer->remove("test_property"));
        $this->assertFalse(isset($_SESSION["my_namespace.test_property"]));
    }

    /** @covers Brickoo\Component\Session\SessionContainer::getIterator */
    public function testGetIterator() {
        $this->assertInstanceOf("\\ArrayIterator", $this->sessionContainer->getIterator());
    }

    /** @covers Brickoo\Component\Session\SessionContainer::count */
    public function testCountEntries() {
        $this->assertEquals(1, count($this->sessionContainer));
    }

}
