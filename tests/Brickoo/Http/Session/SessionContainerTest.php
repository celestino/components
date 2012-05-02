<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Http\Session\Container;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * SessionContainerTest
     *
     * Test suite for the Container class.
     * Using the SessionManager the session.autostart configuration should be set to zero.
     * @see Brickoo\Http\Session\Container
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SessionContainerTest extends PHPUnit_Framework_TestCase {

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
            $_SESSION = array('my_namespace.test_property' => 'some value');
            $this->Container = new Container('my_namespace');
        }

        /**
         * Test if a Container instance can be created and implements the Session\Interfaces\Container.
         * @covers Brickoo\Http\Session\Container::__construct
         */
        public function testConstruct() {
            $this->assertInstanceOf
            (
                'Brickoo\Http\Session\Interfaces\Container',
                $Container = new Container('some_namespace')
            );
        }

        /**
         * Test if trying to create a Container instance with a wrong namespace type throws an exception.
         * @covers Brickoo\Http\Session\Container::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructArgumentException() {
            $Container = new Container('wrong.namespace?type');
        }

        /**
         * Test if the session property is recognized.
         * @covers Brickoo\Http\Session\Container::has
         */
        public function testHas() {
            $this->assertTrue($this->Container->has('test_property'));
            $this->assertFalse($this->Container->has('not_available'));
        }

        /**
         * Test if the session property value can be retrieved and if othrewise the default value is returned.
         * @covers Brickoo\Http\Session\Container::get
         */
        public function testGet() {
            $this->assertEquals('some value', $this->Container->get('test_property'));
            $this->assertEquals('default value', $this->Container->get('not_available', 'default value'));
        }

        /**
         * Test if a session property can be set and the Container reference is returned.
         * @covers Brickoo\Http\Session\Container::set
         */
        public function testSet() {
            $this->assertSame($this->Container, $this->Container->set('new_property', 'new value'));
            $this->assertTrue(($_SESSION['my_namespace.new_property'] == 'new value'));
        }

        /**
         * Test if a session property can be removed and the Container reference is returned.
         * @covers Brickoo\Http\Session\Container::remove
         */
        public function testRemove() {
            $this->assertSame($this->Container, $this->Container->remove('test_property'));
            $this->assertFalse(isset($_SESSION['my_namespace.test_property']));
        }

        /**
         * Test if using the magic method __get the session property value can be retrieved.
         * @covers Brickoo\Http\Session\Container::__get
         */
        public function test__get() {
            $this->assertEquals('some value', $this->Container->test_property);
        }

        /**
         * Test if using the magic method __set the session value can be stored.
         * @covers Brickoo\Http\Session\Container::__set
         */
        public function test__set() {
            $this->Container->new_property = 'some new value';
            $this->assertTrue(($_SESSION['my_namespace.new_property'] == 'some new value'));
        }

        /**
         * Test if using the magic method __unset the session property can be removed.
         * @covers Brickoo\Http\Session\Container::__unset
         */
        public function test__unset() {
            unset($this->Container->test_property);
            $this->assertFalse(isset($_SESSION['my_namespace.test_property']));
        }

        /**
         * Test if using the magic method __isset the session property can be checked if exists.
         * @covers Brickoo\Http\Session\Container::__isset
         */
        public function test__isset() {
            $this->assertTrue(isset($this->Container->test_property));
        }

    }