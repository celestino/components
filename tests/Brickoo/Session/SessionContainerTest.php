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

namespace Brickoo\Tests\Session;

use Brickoo\Session\SessionContainer,
    PHPUnit_Framework_TestCase;

/**
 * SessionContainerTest
 *
 * Test suite for the SessionContainer class.
 * @see Brickoo\Session\SessionContainer
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SessionContainerTest extends PHPUnit_Framework_TestCase {

    /**
     * Holds an instance of the Container implementing the Session\Interfaces\Container.
     * @var object
     */
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
     * @covers Brickoo\Session\SessionContainer::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructArgumentException() {
        $Container = new SessionContainer(array("wrongType"));
    }

    /**
     * @covers Brickoo\Session\SessionContainer::has
     * @covers Brickoo\Session\SessionContainer::getNamespace
     */
    public function testHas() {
        $this->assertTrue($this->sessionContainer->has("test_property"));
        $this->assertFalse($this->sessionContainer->has("not_available"));
    }

    /**
     * @covers Brickoo\Session\SessionContainer::get
     * @covers Brickoo\Session\SessionContainer::getNamespace
     */
    public function testGet() {
        $this->assertEquals("some value", $this->sessionContainer->get("test_property"));
        $this->assertEquals("default value", $this->sessionContainer->get("not_available", "default value"));
    }

    /**
     * @covers Brickoo\Session\SessionContainer::set
     * @covers Brickoo\Session\SessionContainer::getNamespace
     */
    public function testSet() {
        $this->assertSame($this->sessionContainer, $this->sessionContainer->set("new_property", "new value"));
        $this->assertTrue(($_SESSION["my_namespace.new_property"] == "new value"));
    }

    /**
     * @covers Brickoo\Session\SessionContainer::remove
     * @covers Brickoo\Session\SessionContainer::getNamespace
     */
    public function testRemove() {
        $this->assertSame($this->sessionContainer, $this->sessionContainer->remove("test_property"));
        $this->assertFalse(isset($_SESSION["my_namespace.test_property"]));
    }

}