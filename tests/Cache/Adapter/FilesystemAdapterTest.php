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

namespace Brickoo\Tests\Component\Cache\Adapter;

use Brickoo\Component\Cache\Adapter\FilesystemAdapter;
use PHPUnit_Framework_TestCase;

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
        $failureCacheDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR."DOES_NOT_EXIST".DIRECTORY_SEPARATOR;

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
        return dirname(__FILE__) . DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR;
    }

}
