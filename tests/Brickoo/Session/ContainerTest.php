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

    namespace Tests\Brickoo\Session;

    use Brickoo\Session\SessionContainer;

    /**
     * ContainerTest
     *
     * Test suite for the Container class.
     * @see Brickoo\Session\Container
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ContainerTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Container implementing the Session\Interfaces\Container.
         * @var object
         */
        protected $Container;

        /**
         * Set up the Container instance used.
         * Clean up the global $_SESSION variable.
         * @return void
         */
        public function setUp() {
            $_SESSION = array("my_namespace.test_property" => "some value");
            $this->Container = new SessionContainer("my_namespace");
        }

        /**
         * @covers Brickoo\Session\Container::__construct
         */
        public function testConstructor() {
            $this->assertInstanceOf(
                "Brickoo\Session\Interfaces\Container",
                $Container = new SessionContainer("some_namespace")
            );
        }

        /**
         * @covers Brickoo\Session\Container::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructArgumentException() {
            $Container = new SessionContainer(array("wrongType"));
        }

        /**
         * @covers Brickoo\Session\Container::has
         * @covers Brickoo\Session\Container::getNamespace
         */
        public function testHas() {
            $this->assertTrue($this->Container->has("test_property"));
            $this->assertFalse($this->Container->has("not_available"));
        }

        /**
         * @covers Brickoo\Session\Container::get
         * @covers Brickoo\Session\Container::getNamespace
         */
        public function testGet() {
            $this->assertEquals("some value", $this->Container->get("test_property"));
            $this->assertEquals("default value", $this->Container->get("not_available", "default value"));
        }

        /**
         * @covers Brickoo\Session\Container::set
         * @covers Brickoo\Session\Container::getNamespace
         */
        public function testSet() {
            $this->assertSame($this->Container, $this->Container->set("new_property", "new value"));
            $this->assertTrue(($_SESSION["my_namespace.new_property"] == "new value"));
        }

        /**
         * @covers Brickoo\Session\Container::remove
         * @covers Brickoo\Session\Container::getNamespace
         */
        public function testRemove() {
            $this->assertSame($this->Container, $this->Container->remove("test_property"));
            $this->assertFalse(isset($_SESSION["my_namespace.test_property"]));
        }

    }