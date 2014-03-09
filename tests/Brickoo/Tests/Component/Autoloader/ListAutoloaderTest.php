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

namespace Brickoo\Tests\Component\Autoloader;

use Brickoo\Component\Autoloader\ListAutoloader,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the ListAutoloader class.
 * @see Brickoo\Component\Autoloader\ListAutoloader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ListAutoloaderTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Autoloader\ListAutoloader::__construct */
    public function testConstructor() {
        $expectedClassList = array(
            "Foo" => dirname(__FILE__),
            "Bar" => dirname(__FILE__),
        );
        $listAutoloader = new ListAutoloader($expectedClassList);
        $this->assertInstanceOf("\\Brickoo\\Component\\Autoloader\\Autoloader", $listAutoloader);
        $this->assertAttributeEquals($expectedClassList, "classes", $listAutoloader);
    }

    /** @covers Brickoo\Component\Autoloader\ListAutoloader::registerClass */
    public function testRegisterClass() {
        $expectedClasses = array("Foo" => dirname(__FILE__));
        $listAutoloader = new ListAutoloader();
        $this->assertSame($listAutoloader, $listAutoloader->registerClass("Foo", dirname(__FILE__)));
        $this->assertAttributeEquals($expectedClasses, "classes", $listAutoloader);
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::registerClass
     * @expectedException InvalidArgumentException
     */
    public function testRegisterClassThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass(["wrongType"], "some location");
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::registerClass
     * @covers Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     * @expectedException Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     */
    public function testRegisterClassThrowsFileDoesNotExistException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", "./doesNotExists");
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::registerClass
     * @covers Brickoo\Component\Autoloader\Exception\DuplicateClassRegistrationException
     * @expectedException Brickoo\Component\Autoloader\Exception\DuplicateClassRegistrationException
     */
    public function testRegisterClassThrowsDuplicateClassRegistrationException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
    }

    /** @covers Brickoo\Component\Autoloader\ListAutoloader::unregisterClass */
    public function testUnregisterClass() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $this->assertSame($listAutoloader, $listAutoloader->unregisterClass("Foo"));
        $this->assertAttributeEquals(array(), "classes", $listAutoloader);
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::unregisterClass
     * @expectedException InvalidArgumentException
     */
    public function testUnregisterClassThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->unregisterClass(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::unregisterClass
     * @covers Brickoo\Component\Autoloader\Exception\ClassNotRegisteredException
     * @expectedException Brickoo\Component\Autoloader\Exception\ClassNotRegisteredException
     */
    public function testUnregisterClassThrowsClassNotRegisteredException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->unregisterClass("Foo");
    }

    /** @covers Brickoo\Component\Autoloader\ListAutoloader::isClassRegistered */
    public function testIsClassRegistered() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $this->assertTrue($listAutoloader->isClassRegistered("Foo"));
        $this->assertFalse($listAutoloader->isClassRegistered("Bar"));
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::isClassRegistered
     * @expectedException InvalidArgumentException
     */
    public function testIsClassRegisteredThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->isClassRegistered(["wrongType"]);
    }

    /** @covers Brickoo\Component\Autoloader\ListAutoloader::getRegisteredClasses */
    public function testGetRegisteredClasses() {
        $expectedClasses = array("Foo" => dirname(__FILE__));
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("Foo", dirname(__FILE__));
        $this->assertEquals($expectedClasses, $listAutoloader->getRegisteredClasses());
    }

    /** @covers Brickoo\Component\Autoloader\ListAutoloader::load */
    public function testLoadClass() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->registerClass("ListLoadableClass", dirname(__FILE__) . "/Assets/ListLoadableClass.php");
        $this->assertTrue($listAutoloader->load("ListLoadableClass"));
        $this->assertTrue(class_exists("Brickoo\Tests\Component\Autoloader\Assets\ListLoadableClass"));
    }

    /** @covers Brickoo\Component\Autoloader\ListAutoloader::load */
    public function testLoadClassIsNotResponsible() {
        $listAutoloader = new ListAutoloader();
        $this->assertFalse($listAutoloader->load("NotListenedClass"));
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::load
     * @expectedException InvalidArgumentException
     */
    public function testLoadClassThrowsInvalidArgumentException() {
        $listAutoloader = new ListAutoloader();
        $listAutoloader->load(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Autoloader\ListAutoloader::load
     * @covers Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     * @expectedException Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     */
    public function testLoadClassThrowsFileDoesNotExistException() {
        $listAutoloader = new ListAutoloader(array("Foo" => "FooDoesNotExists.php"));
        $listAutoloader->load("Foo");
    }

}
