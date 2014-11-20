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

use Brickoo\Component\IO\Stream\StreamSeeker;
use PHPUnit_Framework_TestCase;

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
