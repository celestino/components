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

use Brickoo\Autoloader\NamespaceAutoloader;

/**
 * Test suite for the NamespaceAutoloader class.
 * @see Brickoo\Autoloader\NamespaceAutoloader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class NamespaceAutoloaderTest extends \PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Autoloader\NamespaceAutoloader::__construct */
    public function testConstructor() {
        $expectedNamespaces = array("Tests" => "/path/to/tests");
        $NamespaceAutoloader = new NamespaceAutoloader($expectedNamespaces);
        $this->assertInstanceOf("\\Brickoo\Autoloader\\Autoloader", $NamespaceAutoloader);
        $this->assertAttributeEquals($expectedNamespaces, "namespaces", $NamespaceAutoloader);

    }

    /** @covers Brickoo\Autoloader\NamespaceAutoloader::registerNamespace */
    public function AtestRegisterNamespace() {
        $expectedNamespace = array("TestNamespace" => dirname(__FILE__));
        $NamespaceAutoloader = new NamespaceAutoloader();
        $this->assertSame($NamespaceAutoloader, $NamespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__)));
        $this->assertAttributeEquals($expectedNamespace, "namespaces", $NamespaceAutoloader);
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::registerNamespace
     * @expectedException InvalidArgumentException
     */
    public function testRegisterNamespaceThrowsInvalidArgumentException() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace(["wrongType"], null);
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::registerNamespace
     * @covers Brickoo\Autoloader\Exception\DirectoryDoesNotExistException
     * @expectedException Brickoo\Autoloader\Exception\DirectoryDoesNotExistException
     */
    public function testRegisterNamespaceThrowsDirectoryDoesNotExistException() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("brickoo", "path/does/not/exist");
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::registerNamespace
     * @covers Brickoo\Autoloader\Exception\DuplicateNamespaceRegistrationException
     * @expectedException Brickoo\Autoloader\Exception\DuplicateNamespaceRegistrationException
     */
    public function testRegisterDuplicateNamespaceThrowsDuplicateNamespaceRegistrationException() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $NamespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
    }

    /** @covers Brickoo\Autoloader\NamespaceAutoloader::unregisterNamespace */
    public function testUnregisterNamespace() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertSame($NamespaceAutoloader, $NamespaceAutoloader->unregisterNamespace("TestNamespace"));
        $this->assertAttributeEquals(array(), "namespaces", $NamespaceAutoloader);
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::unregisterNamespace
     * @covers Brickoo\Autoloader\Exception\NamespaceNotRegisteredException
     * @expectedException Brickoo\Autoloader\Exception\NamespaceNotRegisteredException
     */
    public function testUnregisterNamespaceThrowsNamespaceNotRegisteredException() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->unregisterNamespace("NotRegisteredNamespace");
    }

    /** @covers Brickoo\Autoloader\NamespaceAutoloader::getRegisteredNamespaces */
    public function testGetRegisteredNamespaces() {
        $expectedNamespaces = array("TestNamespace" => dirname(__FILE__));
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertEquals($expectedNamespaces, $NamespaceAutoloader->getRegisteredNamespaces());
    }

    /** @covers Brickoo\Autoloader\NamespaceAutoloader::isNamespaceRegistered */
    public function testIsNamespaceRegistered() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertTrue($NamespaceAutoloader->isNamespaceRegistered("TestNamespace"));
        $this->assertFalse($NamespaceAutoloader->isNamespaceRegistered("OtherNamespace"));
    }

    /** @covers Brickoo\Autoloader\NamespaceAutoloader::isNamespaceRegistered */
    public function testIsNamespaceRegisteredFails() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertFalse($NamespaceAutoloader->isNamespaceRegistered("fail"));
        $this->assertTrue($NamespaceAutoloader->isNamespaceRegistered("TestNamespace"));
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::isNamespaceRegistered
     * @expectedException InvalidArgumentException
     */
    public function testIsNamespaceRegisteredThrowsInvalidArgumentException() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->isNamespaceRegistered(["wrongType"]);
    }

    /**         *
     * @covers Brickoo\Autoloader\NamespaceAutoloader::load
     * @covers Brickoo\Autoloader\NamespaceAutoloader::getAbsolutePath
     */
    public function testLoadClass() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("Assets", dirname(__FILE__));
        $this->assertTrue($NamespaceAutoloader->load("Assets\NamespaceLoadableClass"));
        $this->assertTrue(class_exists("Brickoo\Tests\Autoloader\Assets\NamespaceLoadableClass"));
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::load
     * @expectedException InvalidArgumentException
     */
    public function testLoadClassThrowsInvalidArgumentException() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->load("\\");
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::load
     * @covers Brickoo\Autoloader\NamespaceAutoloader::getAbsolutePath
     */
    public function testLoadClassReturnsFalseIfNotRegistered() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $this->assertFalse($NamespaceAutoloader->load("Namespace\not\registred"));
    }

    /**
     * @covers Brickoo\Autoloader\NamespaceAutoloader::load
     * @covers Brickoo\Autoloader\Exception\FileDoesNotExistException
     * @expectedException Brickoo\Autoloader\Exception\FileDoesNotExistException
     */
    public function testFileDoesNotExist() {
        $NamespaceAutoloader = new NamespaceAutoloader();
        $NamespaceAutoloader->registerNamespace("Brickoo", dirname(__FILE__));
        $NamespaceAutoloader->load("Brickoo\DoesNotExist");
    }

 }