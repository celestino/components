<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Tests\Component\IO\Stream;

use Brickoo\Component\IO\Stream\FileStream;
use Brickoo\Component\IO\Stream\FileStreamConfig;
use PHPUnit_Framework_TestCase;

/**
 * FileStreamConfigTest
 *
 * Test suite for the FileStreamConfig class.
 * @see Brickoo\Component\IO\Stream\FileStreamConfig
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class FileStreamConfigTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\IO\Stream\FileStreamConfig::__construct */
    public function testCreateFileStreamConfig() {
        $fileStreamConfig = new FileStreamConfig("/path/to/file", FileStream::MODE_READ, true, array());
        $this->assertInstanceOf("\\Brickoo\\Component\\IO\\Stream\\FileStreamConfig", $fileStreamConfig);
    }

    /** @covers Brickoo\Component\IO\Stream\FileStreamConfig::getFilename */
    public function testGetFilename() {
        $filename = "/path/to/file";
        $fileStreamConfig = new FileStreamConfig($filename, FileStream::MODE_READ);
        $this->assertEquals($filename, $fileStreamConfig->getFilename());
    }

    /** @covers Brickoo\Component\IO\Stream\FileStreamConfig::getMode */
    public function testGetMode() {
        $mode = FileStream::MODE_READ + FileStream::MODE_WRITE;
        $fileStreamConfig = new FileStreamConfig("/path/to/file", $mode);
        $this->assertEquals($mode, $fileStreamConfig->getMode());
    }

    /** @covers Brickoo\Component\IO\Stream\FileStreamConfig::shouldUseIncludePath */
    public function testUseIncludePathFlag() {
        $fileStreamConfig = new FileStreamConfig("/path/to/file", FileStream::MODE_READ, true);
        $this->assertTrue($fileStreamConfig->shouldUseIncludePath());
    }

    /** @covers Brickoo\Component\IO\Stream\FileStreamConfig::getContext */
    public function testGetContext() {
        $context = array();
        $fileStreamConfig = new FileStreamConfig("/path/to/file", FileStream::MODE_READ, false, $context);
        $this->assertEquals($context, $fileStreamConfig->getContext());
    }

}
