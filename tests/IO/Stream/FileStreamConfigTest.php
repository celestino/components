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
