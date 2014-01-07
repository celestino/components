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

namespace Brickoo\Tests\Autoloader;

use Brickoo\Autoloader\ListAutoloader,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the ListAutoloader class.
 * @see Brickoo\Autoloader\ListAutoloader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListAutoloaderTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Autoloader\ListAutoloader::__construct */
    public function testConstructor() {
        $expectedClassList = array(
            "Foo" => dirname(__FILE__),
            "Bar" => dirname(__FILE__),
        );
        $listAutoloader = new ListAutoloader($expectedClassList);
        $this->assertInstanceOf("\\Brickoo\\Autoloader\\Autoloader", $listAutoloader);
        $this->assertAttributeEquals($expectedClassList, "classes", $listAutoloader);
    }

    /** @covers Brickoo\Autoloader\ListAutoloader::registerClass */
    public function testRegisterClass() {
        $expectedClasses = array("Foo" => dirname(__FILE__));
        $listAutoloader = new ListAutoloader();
        $this->assertSame($listAutoloader, $listAutoloader->registerClass("Foo", dirname(__FILE__)));
        $this->assertAttributeEquals($expectedClasses, "classes", $listAutoloader);
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::registerClass
     * @expectedException InvalidArgumentException
     */
    public function testRegisterClassThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass(["wrongType"], "some location");
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::registerClass
     * @covers Brickoo\Autoloader\Exception\FileDoesNotExistException
     * @expectedException Brickoo\Autoloader\Exception\FileDoesNotExistException
     */
    public function testRegisterClassThrowsFileDoesNotExistException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", "./doesNotExists");
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::registerClass
     * @covers Brickoo\Autoloader\Exception\DuplicateClassRegistrationException
     * @expectedException Brickoo\Autoloader\Exception\DuplicateClassRegistrationException
     */
    public function testRegisterClassThrowsDuplicateClassRegistrationException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
    }

    /** @covers Brickoo\Autoloader\ListAutoloader::unregisterClass */
    public function testUnregisterClass() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $this->assertSame($listAutoloader, $listAutoloader->unregisterClass("Foo"));
        $this->assertAttributeEquals(array(), "classes", $listAutoloader);
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::unregisterClass
     * @expectedException InvalidArgumentException
     */
    public function testUnregisterClassThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->unregisterClass(["wrongType"]);
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::unregisterClass
     * @covers Brickoo\Autoloader\Exception\ClassNotRegisteredException
     * @expectedException Brickoo\Autoloader\Exception\ClassNotRegisteredException
     */
    public function testUnregisterClassThrowsClassNotRegisteredException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->unregisterClass("Foo");
    }

    /** @covers Brickoo\Autoloader\ListAutoloader::isClassRegistered */
    public function testIsClassRegistered() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $this->assertTrue($listAutoloader->isClassRegistered("Foo"));
        $this->assertFalse($listAutoloader->isClassRegistered("Bar"));
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::isClassRegistered
     * @expectedException InvalidArgumentException
     */
    public function testIsClassRegisteredThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->isClassRegistered(["wrongType"]);
    }

    /** @covers Brickoo\Autoloader\ListAutoloader::getRegisteredClasses */
    public function testGetRegisteredClasses() {
        $expectedClasses = array("Foo" => dirname(__FILE__));
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $this->assertEquals($expectedClasses, $listAutoloader->getRegisteredClasses());
    }

    /** @covers Brickoo\Autoloader\ListAutoloader::load */
    public function testLoadClass() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("ListLoadableClass", dirname(__FILE__) ."/Assets/ListLoadableClass.php");
        $this->assertTrue($listAutoloader->load("ListLoadableClass"));
        $this->assertTrue(class_exists("Brickoo\Tests\Autoloader\Assets\ListLoadableClass"));
    }

    /** @covers Brickoo\Autoloader\ListAutoloader::load */
    public function testLoadClassIsNotResponsible() {
        $listAutoloader = new ListAutoloader();
        $this->assertFalse($listAutoloader->load("NotListenedClass"));
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::load
     * @expectedException InvalidArgumentException
     */
    public function testLoadClassThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->load(["wrongType"]);
    }

    /**
     * @covers Brickoo\Autoloader\ListAutoloader::load
     * @covers Brickoo\Autoloader\Exception\FileDoesNotExistException
     * @expectedException Brickoo\Autoloader\Exception\FileDoesNotExistException
     */
    public function testLoadClassThrowsFileDoesNotExistException() {
        $listAutoloader = new ListAutoloader(array("Foo" => "FooDoesNotExists.php"));
        $listAutoloader->load("Foo");
    }

}