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

namespace Brickoo\Tests\Component\Cache\Adapter;

use Brickoo\Component\Cache\Adapter\FilesystemAdapter,
    Brickoo\Component\Filesystem\File,
    PHPUnit_Framework_TestCase;

/**
 * FilesystemAdapterTest
 *
 * Test suite for the FilesystemAdapter class.
 * Some of the test cases are using the PHP temporary directory for the cache files.
 * @see Brickoo\Component\Cache\Adapter\FilesystemAdapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class FilesystemAdapterTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::__construct */
    public function testConstructorImplementsInterface() {
        $file = $this->getFileStub();
        $filesystemAdapter = new FilesystemAdapter($file, getcwd(), false, ".test");
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Adapter\\Adapter", $filesystemAdapter);
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::set
     * @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::getCacheFilePath
     */
    public function testSetCacheContentWritesToFilesystem() {
        $content = "content";
        $file = $this->getFileStub();
        $file->expects($this->once())
                   ->method("open")
                   ->with(getcwd(). DIRECTORY_SEPARATOR ."some_identifier.cache", "w")
                   ->will($this->returnSelf());
        $file->expects($this->once())
                   ->method("write")
                   ->will($this->returnValue(strlen($content)));
        $file->expects($this->once())
                   ->method("close")
                   ->will($this->returnSelf());

        $filesystemAdapter = new FilesystemAdapter($file, getcwd(), true, ".cache");
        $this->assertSame($filesystemAdapter, $filesystemAdapter->set("some_identifier", $content, 0));
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::get
     * @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::delete
     */
    public function testGetFileLifetimeExpiredReturnsNullInsteadOfCachedContent() {
        $cacheDirectory = $this->getAssetsDirectoryPath();
        $cacheFileName = "cached_content";
        $cacheFileSuffix = ".cache";

        $file = $this->getFileStub();
        $file->expects($this->once())
                   ->method("open")
                   ->with($cacheDirectory . $cacheFileName . $cacheFileSuffix, "r")
                   ->will($this->returnSelf());
        $file->expects($this->once())
                   ->method("read")
                   ->with(strlen(date(FilesystemAdapter::LIFETIME_FORMAT)))
                   ->will($this->returnValue(date(FilesystemAdapter::LIFETIME_FORMAT, time()-10)));
        $file->expects($this->once())
                   ->method("close")
                   ->will($this->returnSelf());

        $filesystemAdapter = new FilesystemAdapter($file, $cacheDirectory, false, $cacheFileSuffix);
        $this->assertNull($filesystemAdapter->get($cacheFileName));
    }

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::get */
    public function testGetCacheContentReturnsCachedContent() {
        $cacheDirectory = $this->getAssetsDirectoryPath();
        $cacheFileName = "cached_content";
        $cacheFileSuffix = ".cache";

        $filesystemAdapter = new FilesystemAdapter(new File(), $cacheDirectory, true, $cacheFileSuffix);
        $this->assertEquals(["cached content"], $filesystemAdapter->get($cacheFileName));
    }

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::delete */
    public function testDeleteCacheFileFromTempDirectory() {
        $cacheDirectory = $this->getAssetsDirectoryPath();
        $cacheFileName = "cached_content";
        $cacheFileSuffix = ".cache";
        copy(
            $cacheDirectory . $cacheFileName .$cacheFileSuffix,
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix
        );

        $filesystemAdapter = new FilesystemAdapter($this->getFileStub(), sys_get_temp_dir(), false, $cacheFileSuffix);
        $this->assertTrue(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
        $this->assertSame($filesystemAdapter, $filesystemAdapter->delete($cacheFileName));
        $this->assertFalse(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));

    }

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::flush */
    public function testFlushCacheFilesFromTempDirectory() {
        $cacheDirectory = $this->getAssetsDirectoryPath();
        $cacheFileName = "cached_content";
        $cacheFileSuffix = ".cache";
        copy(
            $cacheDirectory . $cacheFileName .$cacheFileSuffix,
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix
        );

        $filesystemAdapter = new FilesystemAdapter($this->getFileStub(), sys_get_temp_dir(), false, $cacheFileSuffix);
        $this->assertTrue(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
        $this->assertSame($filesystemAdapter, $filesystemAdapter->flush());
        $this->assertFalse(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
    }

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::isReady */
    public function testIsReady() {
        $failureCacheDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR ."DOES_NOT_EXIST". DIRECTORY_SEPARATOR;
        $cacheFileSuffix =".cache";

        $filesystemAdapter = new FilesystemAdapter($this->getFileStub(), $failureCacheDirectory, false, $cacheFileSuffix);
        $this->assertFalse($filesystemAdapter->isReady());

        $filesystemAdapter = new FilesystemAdapter($this->getFileStub(), sys_get_temp_dir(), false, $cacheFileSuffix);
        $this->assertTrue($filesystemAdapter->isReady());
    }

    /**
     * Returns a file stub.
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFileStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Filesystem\\File")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns the test assets directory path.
     * @return string the assets directory path
     */
    private function getAssetsDirectoryPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR ."assets". DIRECTORY_SEPARATOR;
    }

}