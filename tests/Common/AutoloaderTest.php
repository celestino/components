<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
