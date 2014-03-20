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

use Brickoo\Component\Autoloader\IncludePathAutoloader,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the IncludePathAutoloader class.
 * @see Brickoo\Component\Autoloader\IncludePathAutoloader
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class IncludePathAutoloaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::__construct
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::setIncludePath
     */
    public function testConstructor() {
        $includePathAutoloader = new IncludePathAutoloader(__DIR__, true);
        $this->assertInstanceOf("\\Brickoo\\Component\\Autoloader\\Autoloader", $includePathAutoloader);
        $this->assertAttributeEquals(__DIR__, "includePath", $includePathAutoloader);
    }

    /**
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::__construct
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::setIncludePath
     * @expectedException \InvalidArgumentException
     */
    public function testSetIncludePathInvalidArgumentThrowsException() {
        new IncludePathAutoloader(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::__construct
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::setIncludePath
     * @covers Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     * @expectedException \Brickoo\Component\Autoloader\Exception\DirectoryDoesNotExistException
     */
    public function testSetIncludePathInvalidPathThrowsException() {
        new IncludePathAutoloader("./path/does/not/exist");
    }

    /**
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::load
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::getAbsolutePath
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::getTranslatedClassPath
     */
    public function testLoadClassWithDefaultPath() {
        $includePathAutoloader = new IncludePathAutoloader(__DIR__);
        $this->assertTrue($includePathAutoloader->load("Assets\\DefaultPathLoadableClass"));
        $this->assertTrue(class_exists("Brickoo\\Tests\\Component\\Autoloader\\Assets\\DefaultPathLoadableClass"));
    }

    /**
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::load
     * @expectedException \InvalidArgumentException
     */
    public function testLoadClassThrowsInvalidArgumentException() {
        $includePathAutoloader = new IncludePathAutoloader(__DIR__);
        $includePathAutoloader->load("\\");
    }

    /**
     * @covers Brickoo\Component\Autoloader\IncludePathAutoloader::load
     * @covers Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     * @expectedException \Brickoo\Component\Autoloader\Exception\FileDoesNotExistException
     */
    public function testFileDoesNotExist() {
        $includePathAutoloader = new IncludePathAutoloader(__DIR__);
        $includePathAutoloader->load("DoesNotExist.php");
    }

 }
