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

namespace Brickoo\Tests\Component\IO;

use Brickoo\Component\IO\Stream\StreamSeeker,
    PHPUnit_Framework_TestCase;

/**
 * StreamSeekerTest
 *
 * Test suite for the StreamSeeker class.
 * @see Brickoo\Component\IO\Stream\StreamSeeker
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StreamSeekerTest extends PHPUnit_Framework_TestCase {

    /** @var resource */
    private $streamResource;

    /** {@inheritdoc} */
    public function setUp() {
        $this->streamResource = fopen("php://memory", "rb+");
        fwrite($this->streamResource, "1234567890");
        rewind($this->streamResource);
    }

    /** {@inheritdoc} */
    public function tearDown() {
        if (is_resource($this->streamResource)) {
            fclose($this->streamResource);
            $this->streamResource = null;
        }
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::__construct
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::tell
     */
    public function testStreamSeekerTellCurrentPosition() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        $this->assertEquals(0, $streamSeeker->tell());
        fseek($this->streamResource, 5, SEEK_CUR);
        $this->assertEquals(5, $streamSeeker->tell());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::tell
     * @covers \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     */
    public function testStreamSeekerTellWithInvalidResourceThrowsException() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        fclose($this->streamResource);
        $streamSeeker->tell();
    }

    /** @covers Brickoo\Component\IO\Stream\StreamSeeker::rewind */
    public function testStreamSeekerRewindPointerPosition() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        fseek($this->streamResource, 5, SEEK_CUR);
        $this->assertEquals(5, $streamSeeker->tell());
        $this->assertTrue($streamSeeker->rewind());
        $this->assertEquals(0, $streamSeeker->tell());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::rewind
     * @covers \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     */
    public function testStreamSeekerRewindWithInvalidResourceThrowsException() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        fclose($this->streamResource);
        $streamSeeker->rewind();
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::seekTo
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::processSeek
     */
    public function testStreamSeekerMovePointerToPosition() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        $this->assertTrue($streamSeeker->seekTo(5));
        $this->assertEquals(5, $streamSeeker->tell());
        $this->assertTrue($streamSeeker->seekTo(0));
        $this->assertEquals(0, $streamSeeker->tell());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::seek
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::processSeek
     */
    public function testStreamSeekerMovePointerFromCurrentPosition() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        $this->assertTrue($streamSeeker->seek(5));
        $this->assertEquals(5, $streamSeeker->tell());
        $this->assertTrue($streamSeeker->seek(1));
        $this->assertEquals(6, $streamSeeker->tell());
        $this->assertTrue($streamSeeker->seek(-1));
        $this->assertEquals(5, $streamSeeker->tell());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::seekEnd
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::processSeek
     */
    public function testStreamSeekerMovePointerFromEndForward() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        $this->assertTrue($streamSeeker->seekEnd(0));
        $this->assertEquals(10, $streamSeeker->tell());
    }

    /**
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::seek
     * @covers Brickoo\Component\IO\Stream\StreamSeeker::processSeek
     * @covers \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     * @expectedException \Brickoo\Component\IO\Stream\Exception\InvalidResourceHandleException
     */
    public function testStreamSeekerMovePointerWithInvalidResourceThrowsException() {
        $streamSeeker = new StreamSeeker($this->streamResource);
        fclose($this->streamResource);
        $streamSeeker->seek(1);
    }

}
