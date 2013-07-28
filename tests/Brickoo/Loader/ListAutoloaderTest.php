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

    use Brickoo\Loader\ListAutoloader;

    /**
     * Test suite for the ListAutoloader class.
     * @see Brickoo\Loader\ListAutoloader
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ListAutoloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Loader\ListAutoloader::__construct
         */
        public function testConstructor() {
            $expectedClassList = array(
                'Foo' => dirname(__FILE__),
                'Bar' => dirname(__FILE__),
            );
            $ListAutoloader = new ListAutoloader($expectedClassList);
            $this->assertInstanceOf('Brickoo\Loader\Interfaces\ListAutoloader', $ListAutoloader);
            $this->assertAttributeEquals($expectedClassList, 'classes', $ListAutoloader);
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::registerClass
         */
        public function testRegisterClass() {
            $expectedClasses = array('Foo' => dirname(__FILE__));
            $ListAutoloader = new ListAutoloader();
            $this->assertSame($ListAutoloader, $ListAutoloader->registerClass('Foo',dirname(__FILE__)));
            $this->assertAttributeEquals($expectedClasses, 'classes', $ListAutoloader);
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::registerClass
         * @expectedException InvalidArgumentException
         */
        public function testRegisterClassThrowsInvalidArgumentException() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->registerClass(array('wrongType'), 'some location');
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::registerClass
         * @covers Brickoo\Loader\Exceptions\FileDoesNotExist
         * @expectedException Brickoo\Loader\Exceptions\FileDoesNotExist
         */
        public function testRegisterClassThrowsFileDoesNotExistException() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->registerClass('Foo', './doesNotExists');
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::registerClass
         * @covers Brickoo\Loader\Exceptions\DuplicateClassRegistration
         * @expectedException Brickoo\Loader\Exceptions\DuplicateClassRegistration
         */
        public function testRegisterClassThrowsDuplicateClassRegistrationException() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->registerClass('Foo', dirname(__FILE__));
            $ListAutoloader->registerClass('Foo', dirname(__FILE__));
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::unregisterClass
         */
        public function testUnregisterClass() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->registerClass('Foo', dirname(__FILE__));
            $this->assertSame($ListAutoloader, $ListAutoloader->unregisterClass('Foo'));
            $this->assertAttributeEquals(array(), 'classes', $ListAutoloader);
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::unregisterClass
         * @expectedException InvalidArgumentException
         */
        public function testUnregisterClassThrowsInvalidArgumentException() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->unregisterClass(array('wrongType'));
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::unregisterClass
         * @covers Brickoo\Loader\Exceptions\ClassNotRegistered
         * @expectedException Brickoo\Loader\Exceptions\ClassNotRegistered
         */
        public function testUnregisterClassThrowsClassNotRegisteredException() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->unregisterClass('Foo');
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::isClassRegistered
         */
        public function testIsClassRegistered() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->registerClass('Foo', dirname(__FILE__));
            $this->assertTrue($ListAutoloader->isClassRegistered('Foo'));
            $this->assertFalse($ListAutoloader->isClassRegistered('Bar'));
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::isClassRegistered
         * @expectedException InvalidArgumentException
         */
        public function testIsClassRegisteredThrowsInvalidArgumentException() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->isClassRegistered(array('wrongType'));
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::getRegisteredClasses
         */
        public function testGetRegisteredClasses() {
            $expectedClasses = array('Foo' => dirname(__FILE__));
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->registerClass('Foo', dirname(__FILE__));
            $this->assertEquals($expectedClasses, $ListAutoloader->getRegisteredClasses());
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::load
         */
        public function testLoadClass() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->registerClass('ListLoadableClass', dirname(__FILE__) .'/Assets/ListLoadableClass.php');
            $this->assertTrue($ListAutoloader->load('ListLoadableClass'));
            $this->assertTrue(class_exists('Tests\Brickoo\Loader\Assets\ListLoadableClass'));
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::load
         */
        public function testLoadClassIsNotResponsible() {
            $ListAutoloader = new ListAutoloader();
            $this->assertFalse($ListAutoloader->load('NotListenedClass'));
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::load
         * @expectedException InvalidArgumentException
         */
        public function testLoadClassThrowsInvalidArgumentException() {
            $ListAutoloader = new ListAutoloader();
            $ListAutoloader->load(array('wrongType'));
        }

        /**
         * @covers Brickoo\Loader\ListAutoloader::load
         * @covers Brickoo\Loader\Exceptions\FileDoesNotExist
         * @expectedException Brickoo\Loader\Exceptions\FileDoesNotExist
         */
        public function testLoadClassThrowsFileDoesNotExistException() {
            $ListAutoloader = new ListAutoloader(array('Foo' => 'FooDoesNotExists.php'));
            $ListAutoloader->load('Foo');
        }

    }