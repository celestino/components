<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Loader;

    use Brickoo\Loader\NamespaceAutoloader;

    /**
     * Test suite for the NamespaceAutoloader class.
     * @see Brickoo\Loader\NamespaceAutoloader
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class NamespaceAutoloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::__construct
         */
        public function testConstructor() {
            $expectedNamespaces = array('Tests' => '/path/to/tests');
            $NamespaceAutoloader = new NamespaceAutoloader($expectedNamespaces);
            $this->assertInstanceOf('\Brickoo\Loader\Interfaces\NamespaceAutoloader', $NamespaceAutoloader);
            $this->assertAttributeEquals($expectedNamespaces, 'namespaces', $NamespaceAutoloader);

        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::registerNamespace
         */
        public function testRegisterNamespace() {
            $expectedNamespace = array('TestNamespace' => dirname(__FILE__));
            $NamespaceAutoloader = new NamespaceAutoloader();
            $this->assertSame($NamespaceAutoloader, $NamespaceAutoloader->registerNamespace('TestNamespace', dirname(__FILE__)));
            $this->assertAttributeEquals($expectedNamespace, 'namespaces', $NamespaceAutoloader);
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::registerNamespace
         * @expectedException InvalidArgumentException
         */
        public function testRegisterNamespaceThrowsInvalidArgumentException() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace(array('wrongType'), null);
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::registerNamespace
         * @covers Brickoo\Loader\Exceptions\DirectoryDoesNotExist
         * @expectedException Brickoo\Loader\Exceptions\DirectoryDoesNotExist
         */
        public function testRegisterNamespaceThrowsDirectoryDoesNotExistException() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('brickoo', 'path/does/not/exist');
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::registerNamespace
         * @covers Brickoo\Loader\Exceptions\DuplicateNamespaceRegistration
         * @expectedException Brickoo\Loader\Exceptions\DuplicateNamespaceRegistration
         */
        public function testRegisterDuplicateNamespaceThrowsDuplicateNamespaceRegistrationException() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $NamespaceAutoloader->registerNamespace('TestNamespace', dirname(__FILE__));
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::unregisterNamespace
         */
        public function testUnregisterNamespace() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertSame($NamespaceAutoloader, $NamespaceAutoloader->unregisterNamespace('TestNamespace'));
            $this->assertAttributeEquals(array(), 'namespaces', $NamespaceAutoloader);
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::unregisterNamespace
         * @covers Brickoo\Loader\Exceptions\NamespaceNotRegistered
         * @expectedException Brickoo\Loader\Exceptions\NamespaceNotRegistered
         */
        public function testUnregisterNamespaceThrowsNamespaceNotRegisteredException() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->unregisterNamespace('NotRegisteredNamespace');
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::getRegisteredNamespaces
         */
        public function testGetRegisteredNamespaces() {
            $expectedNamespaces = array('TestNamespace' => dirname(__FILE__));
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertEquals($expectedNamespaces, $NamespaceAutoloader->getRegisteredNamespaces());
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::isNamespaceRegistered
         */
        public function testIsNamespaceRegistered() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertTrue($NamespaceAutoloader->isNamespaceRegistered('TestNamespace'));
            $this->assertFalse($NamespaceAutoloader->isNamespaceRegistered('OtherNamespace'));
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::isNamespaceRegistered
         */
        public function testIsNamespaceRegisteredFails() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertFalse($NamespaceAutoloader->isNamespaceRegistered('fail'));
            $this->assertTrue($NamespaceAutoloader->isNamespaceRegistered('TestNamespace'));
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::isNamespaceRegistered
         * @expectedException InvalidArgumentException
         */
        public function testIsNamespaceRegisteredThrowsInvalidArgumentException() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->isNamespaceRegistered(array('wrongType'));
        }

        /**         *
         * @covers Brickoo\Loader\NamespaceAutoloader::load
         * @covers Brickoo\Loader\NamespaceAutoloader::getAbsolutePath
         */
        public function testLoadClass() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('Assets', dirname(__FILE__));
            $this->assertTrue($NamespaceAutoloader->load('Assets\NamespaceLoadableClass'));
            $this->assertTrue(class_exists('Tests\Brickoo\Loader\Assets\NamespaceLoadableClass'));
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::load
         * @expectedException InvalidArgumentException
         */
        public function testLoadClassThrowsInvalidArgumentException() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->load('\\');
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::load
         * @covers Brickoo\Loader\NamespaceAutoloader::getAbsolutePath
         */
        public function testLoadClassReturnsFalseIfNotRegistered() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $this->assertFalse($NamespaceAutoloader->load('Namespace\not\registred'));
        }

        /**
         * @covers Brickoo\Loader\NamespaceAutoloader::load
         * @covers Brickoo\Loader\Exceptions\FileDoesNotExist
         * @expectedException Brickoo\Loader\Exceptions\FileDoesNotExist
         */
        public function testFileDoesNotExist() {
            $NamespaceAutoloader = new NamespaceAutoloader();
            $NamespaceAutoloader->registerNamespace('Brickoo', dirname(__FILE__));
            $NamespaceAutoloader->load('Brickoo\DoesNotExist');
        }

     }