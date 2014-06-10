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
        $filesystemAdapter = new FilesystemAdapter(getcwd(), true, ".test");
        $this->assertInstanceOf("\\Brickoo\\Component\\Cache\\Adapter\\Adapter", $filesystemAdapter);
    }

    /**
     * @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::set
     * @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::getCacheFilePath
     */
    public function testSetCacheContentWritesToFilesystem() {
        $content = "content";
        $filesystemAdapter = new FilesystemAdapter(sys_get_temp_dir());
        $this->assertSame($filesystemAdapter, $filesystemAdapter->set("some_identifier", $content, 0));
        $this->assertEquals(19, strpos(file_get_contents(sys_get_temp_dir().DIRECTORY_SEPARATOR."some_identifier.cache"), $content));
    }

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::get */
    public function testGetFileLifetimeExpiredReturnsNullInsteadOfCachedContent() {
        $filesystemAdapter = new FilesystemAdapter( $this->getAssetsDirectoryPath());
        $this->assertNull($filesystemAdapter->get("expired_content"));
    }

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::get */
    public function testGetCacheContentReturnsCachedContent() {
        $filesystemAdapter = new FilesystemAdapter( $this->getAssetsDirectoryPath());
        $this->assertEquals(["cached content"], $filesystemAdapter->get("cached_content"));
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

        $filesystemAdapter = new FilesystemAdapter(sys_get_temp_dir());
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

        $filesystemAdapter = new FilesystemAdapter(sys_get_temp_dir());
        $this->assertTrue(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
        $this->assertSame($filesystemAdapter, $filesystemAdapter->flush());
        $this->assertFalse(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
    }

    /** @covers Brickoo\Component\Cache\Adapter\FilesystemAdapter::isReady */
    public function testIsReady() {
        $failureCacheDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR ."DOES_NOT_EXIST". DIRECTORY_SEPARATOR;

        $filesystemAdapter = new FilesystemAdapter($failureCacheDirectory);
        $this->assertFalse($filesystemAdapter->isReady());

        $filesystemAdapter = new FilesystemAdapter(sys_get_temp_dir());
        $this->assertTrue($filesystemAdapter->isReady());
    }

    /**
     * Returns the test assets directory path.
     * @return string the assets directory path
     */
    private function getAssetsDirectoryPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR ."assets". DIRECTORY_SEPARATOR;
    }

}
