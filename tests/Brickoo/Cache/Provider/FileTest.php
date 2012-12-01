<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Tests\Brickoo\Cache\Provider;

    use Brickoo\Cache\Provider\File;

    /**
     * FileProviderTest
     *
     * Test suite for the File class.
     * Some of the test cases are using the PHP temporary directory for the cache files.
     * @see Brickoo\Cache\Provider\File
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Cache\Provider\File::__construct
         */
        public function testConstructor() {
            $FileObject = $this->getMock('Brickoo\Filesystem\Interfaces\FileObject');
            $Provider = new File($FileObject, getcwd(), false, ".test");

            $this->assertInstanceOf('Brickoo\Cache\Provider\Interfaces\Provider', $Provider);
            $this->assertAttributeSame($FileObject, 'FileObject', $Provider);
            $this->assertAttributeEquals(getcwd().DIRECTORY_SEPARATOR, 'cacheDirectory', $Provider);
            $this->assertAttributeEquals(false, 'serializeCacheContent', $Provider);
            $this->assertAttributeEquals('.test', 'cacheFileNameSuffix', $Provider);
        }

        /**
         * @covers Brickoo\Cache\Provider\File::set
         * @covers Brickoo\Cache\Provider\File::getCacheFilePath
         */
        public function testSetCacheContent() {
            $content = "content";
            $FileObject = $this->getMock('Brickoo\Filesystem\Interfaces\FileObject');
            $FileObject->expects($this->once())
                       ->method('open')
                       ->with(getcwd(). DIRECTORY_SEPARATOR ."some_identifier.cache", "w")
                       ->will($this->returnSelf());
            $FileObject->expects($this->once())
                       ->method('write')
                       ->will($this->returnValue(strlen($content)));
            $FileObject->expects($this->once())
                       ->method('close')
                       ->will($this->returnSelf());

            $FileProvider = new File($FileObject, getcwd(), true, ".cache");
            $this->assertSame($FileProvider, $FileProvider->set('some_identifier', $content, 0));
        }

        /**
         * @covers Brickoo\Cache\Provider\File::get
         * @covers Brickoo\Cache\Provider\File::getCacheFilePath
         */
        public function testGetNotReadable() {
            $FileObject = $this->getMock('Brickoo\Filesystem\Interfaces\FileObject');

            $FileProvider = new File($FileObject, getcwd());
            $this->assertFalse($FileProvider->get("file_is_not_readable"));
        }

        /**
         * @covers Brickoo\Cache\Provider\File::get
         * @covers Brickoo\Cache\Provider\File::delete
         */
        public function testGetFileLifetimeExpired() {
            $cacheDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR ."assets". DIRECTORY_SEPARATOR;
            $cacheFileName = "cached_content";
            $cacheFileSuffix = ".cache";

            $FileObject = $this->getMock('Brickoo\Filesystem\Interfaces\FileObject');
            $FileObject->expects($this->once())
                       ->method('open')
                       ->with($cacheDirectory . $cacheFileName . $cacheFileSuffix, "r")
                       ->will($this->returnSelf());
            $FileObject->expects($this->once())
                       ->method('read')
                       ->with(File::LIFETIME_BYTES_LENGTH)
                       ->will($this->returnValue(date("YmdHis", time()-10)));
            $FileObject->expects($this->once())
                       ->method('close')
                       ->will($this->returnSelf());

            $FileProvider = new File($FileObject, $cacheDirectory, false, $cacheFileSuffix);
            $this->assertFalse($FileProvider->get($cacheFileName));
        }

        /**
         * @covers Brickoo\Cache\Provider\File::get
         */
        public function testGetCacheContent() {
            $cacheDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR ."assets". DIRECTORY_SEPARATOR;
            $cacheFileName = "cached_content";
            $cacheFileSuffix = ".cache";

            $FileProvider = new File(new \Brickoo\Filesystem\FileObject(), $cacheDirectory, true, $cacheFileSuffix);
            $this->assertEquals(array("cached content"), $FileProvider->get($cacheFileName));
        }

        /**
         * @covers Brickoo\Cache\Provider\File::delete
         */
        public function testDeleteCacheFile() {
            $cacheDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR ."assets". DIRECTORY_SEPARATOR;
            $cacheFileName = "cached_content";
            $cacheFileSuffix =".cache";
            copy(
                $cacheDirectory . $cacheFileName .$cacheFileSuffix,
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix
            );

            $FileObject = $this->getMock("Brickoo\Filesystem\Interfaces\FileObject");
            $FileProvider = new File($FileObject, sys_get_temp_dir(), false, $cacheFileSuffix);

            $this->assertTrue(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
            $this->assertSame($FileProvider, $FileProvider->delete($cacheFileName));
            $this->assertFalse(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));

        }

        /**
         * @covers Brickoo\Cache\Provider\File::flush
         */
        public function testFlushCacheFiles() {
            $cacheDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR ."assets". DIRECTORY_SEPARATOR;
            $cacheFileName = "cached_content";
            $cacheFileSuffix =".cache";
            copy(
                $cacheDirectory . $cacheFileName .$cacheFileSuffix,
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix
            );

            $FileObject = $this->getMock("Brickoo\Filesystem\Interfaces\FileObject");
            $FileProvider = new File($FileObject, sys_get_temp_dir(), false, $cacheFileSuffix);

            $this->assertTrue(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
            $this->assertSame($FileProvider, $FileProvider->flush());
            $this->assertFalse(file_exists(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cacheFileName . $cacheFileSuffix));
        }

    }