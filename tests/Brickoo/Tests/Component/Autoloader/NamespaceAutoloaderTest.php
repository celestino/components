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

use Brickoo\Component\Autoloader\NamespaceAutoloader,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the NamespaceAutoloader class.
 * @see Brickoo\Component\Autoloader\NamespaceAutoloader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class NamespaceAutoloaderTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Autoloader\NamespaceAutoloader::__construct */
    public function testConstructor() {
        $namespaceAutoloader = new NamespaceAutoloader(["TestNamespace" => __DIR__], true);
        $this->assertInstanceOf("\\Brickoo\\Component\\Autoloader\\Autoloader", $namespaceAutoloader);
    }

    /** @covers Brickoo\Component\Autoloader\NamespaceAutoloader::registerNamespace */
    public function testRegisterNamespace() {
        $expectedNamespace = array("TestNamespace" => dirname(__FILE__));
        $namespaceAutoloader = new NamespaceAutoloader();
        $this->assertSame($namespaceAutoloader, $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__)));
        $this->assertAttributeEquals($expectedNamespace, "namespaces", $namespaceAutoloader);
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::registerNamespace
     * @expectedException InvalidArgumentException
     */
    public function testRegisterNamespaceThrowsInvalidArgumentException() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace(["wrongType"], null);
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::registerNamespace
     * @covers Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     * @expectedException Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     */
    public function testRegisterNamespaceThrowsDirectoryDoesNotExistException() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("brickoo", "path/does/not/exist");
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::registerNamespace
     * @covers Brickoo\Component\Autoloader\Exception\DuplicateNamespaceRegistrationException
     * @expectedException Brickoo\Component\Autoloader\Exception\DuplicateNamespaceRegistrationException
     */
    public function testRegisterDuplicateNamespaceThrowsDuplicateNamespaceRegistrationException() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
    }

    /** @covers Brickoo\Component\Autoloader\NamespaceAutoloader::unregisterNamespace */
    public function testUnregisterNamespace() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertSame($namespaceAutoloader, $namespaceAutoloader->unregisterNamespace("TestNamespace"));
        $this->assertAttributeEquals(array(), "namespaces", $namespaceAutoloader);
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::unregisterNamespace
     * @covers Brickoo\Component\Autoloader\Exception\NamespaceNotRegisteredException
     * @expectedException Brickoo\Component\Autoloader\Exception\NamespaceNotRegisteredException
     */
    public function testUnregisterNamespaceThrowsNamespaceNotRegisteredException() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->unregisterNamespace("NotRegisteredNamespace");
    }

    /** @covers Brickoo\Component\Autoloader\NamespaceAutoloader::getRegisteredNamespaces */
    public function testGetRegisteredNamespaces() {
        $expectedNamespaces = array("TestNamespace" => dirname(__FILE__));
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertEquals($expectedNamespaces, $namespaceAutoloader->getRegisteredNamespaces());
    }

    /** @covers Brickoo\Component\Autoloader\NamespaceAutoloader::isNamespaceRegistered */
    public function testIsNamespaceRegistered() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertTrue($namespaceAutoloader->isNamespaceRegistered("TestNamespace"));
        $this->assertFalse($namespaceAutoloader->isNamespaceRegistered("OtherNamespace"));
    }

    /** @covers Brickoo\Component\Autoloader\NamespaceAutoloader::isNamespaceRegistered */
    public function testIsNamespaceRegisteredFails() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertFalse($namespaceAutoloader->isNamespaceRegistered("fail"));
        $this->assertTrue($namespaceAutoloader->isNamespaceRegistered("TestNamespace"));
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::isNamespaceRegistered
     * @expectedException InvalidArgumentException
     */
    public function testIsNamespaceRegisteredThrowsInvalidArgumentException() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->isNamespaceRegistered(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::load
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::getAbsolutePath
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::getNamespaceClassPath
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::getTranslatedClassPath
     */
    public function testLoadClassWithHigherNamespacePathMatch() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("Autoloader", dirname(__FILE__));
        $namespaceAutoloader->registerNamespace("Autoloader\\Assets", dirname(__FILE__)."/Assets");
        $this->assertTrue($namespaceAutoloader->load("Autoloader\\Assets\\NamespaceLoadableClass"));
        $this->assertTrue(class_exists("Brickoo\\Tests\\Component\\Autoloader\\Assets\\NamespaceLoadableClass"));
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::load
     * @expectedException InvalidArgumentException
     */
    public function testLoadClassThrowsInvalidArgumentException() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->load("\\");
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::load
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::getAbsolutePath
     */
    public function testLoadClassReturnsFalseIfNotRegistered() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $this->assertFalse($namespaceAutoloader->load("Namespace\\not\\registred"));
    }

    /**
     * @covers Brickoo\Component\Autoloader\NamespaceAutoloader::load
     * @covers Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     * @expectedException Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     */
    public function testFileDoesNotExist() {
        $namespaceAutoloader = new NamespaceAutoloader();
        $namespaceAutoloader->registerNamespace("Brickoo", dirname(__FILE__));
        $namespaceAutoloader->load("Brickoo\\DoesNotExist");
    }

 }