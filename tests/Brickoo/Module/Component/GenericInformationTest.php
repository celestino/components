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

    namespace Tests\Brickoo\Module\Component;

    use Brickoo\Module\Component\GenericInformation;

    require_once ('PHPUnit/Autoload.php');

    /**
     * GenericInformationTest
     *
     * Test suite for the GenericInformation class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class GenericInformationTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the GenericInformation class.
         * @var GenericInformation
         */
        protected $GenericInformation;

        /**
         * Sets up GenericInformation instance used.
         * @return void
         */
        protected function setUp() {
            $this->GenericInformation = new GenericInformation('testname', 'test Value');
        }

        /**
         * Test if the constructor assigns the properties correctly.
         * @covers Brickoo\Module\Component\GenericInformation
         * @covers Brickoo\Module\Component\GenericInformation::setName
         */
        public function testConstructor() {
            $GenericInformation = new GenericInformation('testName', 'test Value');
            $this->assertAttributeEquals('testname', 'name', $GenericInformation);
            $this->assertAttributeEquals('test Value', 'value', $GenericInformation);
        }

        /**
         * Test if trying to use a wrogn name argument type throws an exception..
         * @covers Brickoo\Module\Component\GenericInformation::setName
         * @covers Brickoo\Module\Component\GenericInformation::setName
         * @expectedException \InvalidArgumentException
         */
        public function testContructorInvalidNameArgument() {
            $GenericInformation = new GenericInformation(array('wrongType'), 'testValue');
        }

        /**
         * Test if the information name can be retrieved.
         * @covers Brickoo\Module\Component\GenericInformation::getName
         */
        public function testGetName() {
            $this->assertEquals('testname', $this->GenericInformation->getName());
        }

        /**
         * Test if the information value can be retrieved.
         * @covers Brickoo\Module\Component\GenericInformation::get
         */
        public function testGet() {
            $this->assertEquals('test Value', $this->GenericInformation->get());
        }

        /**
         * Test if trying to retrieve the information value which is not set throws an exception.
         * @covers Brickoo\Module\Component\GenericInformation::get
         * @expectedException \UnexpectedValueException
         */
        public function testGetValueNotSetException() {
            $GenericInformation = new GenericInformation('someName', null);
            $GenericInformation->get();
        }

        /**
         * Test if the information value can be set and is overwritten.
         * @covers Brickoo\Module\Component\GenericInformation::set
         */
        public function testSet() {
            $this->assertSame($this->GenericInformation, $this->GenericInformation->set('other Value'));
            $this->assertEquals('other Value', $this->GenericInformation->get());
        }

        /**
         * Test if the string represantation of the information object can be retrieved.
         * @covers Brickoo\Module\Component\GenericInformation::toString
         * @covers Brickoo\Module\Component\GenericInformation::__toString
         */
        public function testToString() {
            $this->assertEquals('test Value', $this->GenericInformation->toString());
            $this->assertEquals('test Value', (string)$this->GenericInformation);
        }

    }