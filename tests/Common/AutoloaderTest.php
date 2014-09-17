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

namespace Brickoo\Tests\Component\Common;

use Brickoo\Component\Common\Autoloader;
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the Autoloader class.
 * @see Brickoo\Component\Common\Autoloader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Common\Autoloader::__construct */
    public function testConstructor() {
        $autoloader = new Autoloader(["Vendor\\Library" => "./"]);
        $this->assertAttributeEquals(false, "isRegistered", $autoloader);
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::register
     * @covers Brickoo\Component\Common\Autoloader::unregister
     */
    public function testAutoloaderRegistration() {
        $autoloader = new Autoloader();
        $this->assertAttributeEquals(false, "isRegistered", $autoloader);
        $this->assertSame($autoloader, $autoloader->register());
        $this->assertAttributeEquals(true, "isRegistered", $autoloader);
        $this->assertSame($autoloader, $autoloader->unregister());
        $this->assertAttributeEquals(false, "isRegistered", $autoloader);
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::registerNamespace
     * @covers Brickoo\Component\Common\Autoloader::validateNamespace
     * @covers Brickoo\Component\Common\Autoloader::validateNamespacePath
     */
    public function testRegisterNamespace() {
        $expectedNamespace = array("TestNamespace" => dirname(__FILE__));
        $namespaceAutoloader = new Autoloader();
        $this->assertSame($namespaceAutoloader, $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__)));
        $this->assertAttributeEquals($expectedNamespace, "namespaces", $namespaceAutoloader);
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::registerNamespace
     * @covers Brickoo\Component\Common\Autoloader::validateNamespace
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterNamespaceThrowsInvalidArgumentException() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace(["wrongType"], null);
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::registerNamespace
     * @covers Brickoo\Component\Common\Autoloader::validateNamespacePath
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterNamespacePathThrowsInvalidArgumentException() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("brickoo", ["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::registerNamespace
     * @covers Brickoo\Component\Common\Autoloader::validateNamespacePath
     * @covers Brickoo\Component\Common\Exception\DirectoryDoesNotExistException
     * @expectedException \Brickoo\Component\Common\Exception\DirectoryDoesNotExistException
     */
    public function testRegisterNamespacePathThrowsDirectoryDoesNotExistException() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("brickoo", "path/does/not/exist");
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::registerNamespace
     * @covers Brickoo\Component\Common\Exception\DuplicateNamespaceRegistrationException
     * @expectedException \Brickoo\Component\Common\Exception\DuplicateNamespaceRegistrationException
     */
    public function testRegisterDuplicateNamespaceThrowsDuplicateNamespaceRegistrationException() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
    }

    /** @covers Brickoo\Component\Common\Autoloader::getRegisteredNamespaces */
    public function testGetRegisteredNamespaces() {
        $expectedNamespaces = array("TestNamespace" => dirname(__FILE__));
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertEquals($expectedNamespaces, $namespaceAutoloader->getRegisteredNamespaces());
    }

    /** @covers Brickoo\Component\Common\Autoloader::isNamespaceRegistered */
    public function testIsNamespaceRegistered() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("TestNamespace", dirname(__FILE__));
        $this->assertTrue($namespaceAutoloader->isNamespaceRegistered("TestNamespace"));
        $this->assertFalse($namespaceAutoloader->isNamespaceRegistered("OtherNamespace"));
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::isNamespaceRegistered
     * @expectedException \InvalidArgumentException
     */
    public function testIsNamespaceRegisteredThrowsInvalidArgumentException() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->isNamespaceRegistered(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::load
     * @covers Brickoo\Component\Common\Autoloader::getNamespaceClassPath
     * @covers Brickoo\Component\Common\Autoloader::createClassPath
     * @covers Brickoo\Component\Common\Autoloader::getTranslatedClassPath
     */
    public function testLoadClassWithHigherNamespacePathMatch() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("Autoloader", dirname(__FILE__));
        $namespaceAutoloader->registerNamespace("Autoloader\\Assets", dirname(__FILE__)."/Assets");
        $this->assertTrue($namespaceAutoloader->load("Autoloader\\Assets\\NamespaceLoadableClass"));
        $this->assertTrue(class_exists("Brickoo\\Tests\\Component\\Autoloader\\Assets\\NamespaceLoadableClass"));
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::load
     * @covers Brickoo\Component\Common\Autoloader::getNamespaceClassPath
     * @covers Brickoo\Component\Common\Autoloader::createClassPath
     */
    public function testLoadClassReturnsFalseIfNotRegistered() {
        $namespaceAutoloader = new Autoloader();
        $this->assertFalse($namespaceAutoloader->load("Namespace\\not\\registered"));
    }

    /** @covers Brickoo\Component\Common\Autoloader::load */
    public function testLoadFileDoesNotExistReturnsFalse() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("Brickoo", dirname(__FILE__));
        $this->assertFalse($namespaceAutoloader->load("Brickoo\\DoesNotExist"));
    }

    /**
     * @covers Brickoo\Component\Common\Autoloader::load
     * @covers Brickoo\Component\Common\Autoloader::getTranslatedClassPath
     */
    public function testLoadUnderscoreBasedNamespaceClass() {
        $namespaceAutoloader = new Autoloader();
        $namespaceAutoloader->registerNamespace("Autoloader\\Assets", dirname(__FILE__)."/Assets");

        $this->assertFalse(class_exists("Brickoo\\Tests\\Component\\Autoloader\\Assets\\UnderscoreNamespaceLoadableClass", false));
        $this->assertTrue($namespaceAutoloader->load("Autoloader\\Assets\\UnderscoreNamespaceLoadableClass"));
        $this->assertTrue(class_exists("Brickoo\\Tests\\Component\\Autoloader\\Assets\\UnderscoreNamespaceLoadableClass"));
    }

}
