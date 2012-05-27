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

    use Brickoo\Cache\Provider\File;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * FileTest
     *
     * Test suite for the File class.
     * Some of the test cases are using the PHP temporary directory for the cache files.
     * @see Brickoo\Cache\Provider\File
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * Returns an Stub of the System\FileObject class.
         * @param array $methods the methods to mock
         * @return object FileObject Stub
         */
        public function getFileObjectStub(array $methods = null) {
            return $this->getMock
            (
                'Brickoo\System\FileObject',
                ($methods === null ? null : array_values($methods))
            );
        }

        /**
         * Holds an instance of the File class.
         * @var File
         */
        protected $File;

        /**
         * Set up the File object used.
         * @return void
         */
        protected function setUp() {
            $this->File = new File();
        }

        /**
         * Test if the File implements the Provider.
         * Test if the properties are set.
         * @covers Brickoo\Cache\Provider\File::__construct
         */
        public function testConstruct() {
            $this->assertInstanceOf
            (
                'Brickoo\Cache\Provider\Interfaces\Provider',
                ($Provider = new File(getcwd(), 'test_prefix'))
            );
            $this->assertAttributeEquals(getcwd().DIRECTORY_SEPARATOR, 'directory', $Provider);
            $this->assertAttributeEquals('test_prefix', 'filePrefix', $Provider);
        }

        /**
         * Test if the FileObject can be injected and the File referece is returned.
         * @covers Brickoo\Cache\Provider\File::FileObject
         * @covers Brickoo\Cache\Provider\File::getDependency
         */
        public function testInjectFileObject() {
            $FileObjectStub = $this->getFileObjectStub();
            $this->assertSame($this->File, $this->File->FileObject($FileObjectStub));
            $this->assertAttributeContains($FileObjectStub, 'dependencies', $this->File);

            return $this->File;
        }

        /**
         * Test if the FileObject is lazy initializated and the dependency is returned.
         * @covers Brickoo\Cache\Provider\File::FileObject
         * @covers Brickoo\Cache\Provider\File::getDependency
         */
        public function testGetFileObjectLazyInitalization() {
            $this->assertInstanceOf
            (
                'Brickoo\System\Interfaces\FileObject',
                ($FileObject = $this->File->FileObject())
            );
            $this->assertAttributeContains($FileObject, 'dependencies', $this->File);
        }

        /**
         * Test if the file prefix can be set an the File reference is returned.
         * @covers Brickoo\Cache\Provider\File::setFilePrefix
         */
        public function testSetFilePrefix() {
            $this->assertSame($this->File, $this->File->setFilePrefix('test_'));
            $this->assertAttributeEquals('test_', 'filePrefix', $this->File);

            return $this->File;
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Cache\Provider\File::setFilePrefix
         * @expectedException InvalidArgumentException
         */
        public function testSetFilePrefixArgumentException() {
            $this->File->setFilePrefix(array('wrongType'));
        }

        /**
         * Test if trying to set a wrong file prefix throws an exception.
         * Allowed characters should cover a-z and the underscore.
         * @covers Brickoo\Cache\Provider\File::setFilePrefix
         * @expectedException UnexpectedValueException
         */
        public function testSetFilePrefixValueException() {
            $this->File->setFilePrefix('  wrong . prefix  ');
        }

        /**
         * Test if the file prefix can be retrieved.
         * @covers Brickoo\Cache\Provider\File::getFilePrefix
         * @depends testSetFilePrefix
         */
        public function testGetFilePrefix($File) {
            $this->assertEquals('test_', $File->getFilePrefix());
        }

        /**
         * Test if the cache directory (system temp directory) can be retrieved.
         * @covers Brickoo\Cache\Provider\File::getDirectory
         */
        public function testGetDirectory() {
            $this->assertEquals(realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR, $this->File->getDirectory());
        }

        /**
         * Test if the cache directory can be set and the File reference is returned.
         * The ending slash should be automaticly added.
         * @covers Brickoo\Cache\Provider\File::setDirectory
         */
        public function testSetDirectory() {
            $this->assertSame($this->File, $this->File->setDirectory(sys_get_temp_dir()));
            $this->assertAttributeEquals(realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR, 'directory', $this->File);

            return $this->File;
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Cache\Provider\File::setDirectory
         * @expectedException InvalidArgumentException
         */
        public function testSetDirectoryArgumentException() {
            $this->File->setDirectory(array('wrongType'));
        }

        /**
         * Test if the full file path and name can be retrieved.
         * The white spaces should be converted to underscores.
         * @covers Brickoo\Cache\Provider\File::getFileName
         */
        public function testGetFileName() {
            $this->assertEquals
            (
                realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'cache_some_cache_identifier',
                $this->File->getFileName('some cache identifier')
            );
        }

        /**
         * Tets if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Cache\Provider\File::getFileName
         * @expectedException InvalidArgumentException
         */
        public function testGetFileNameArgumentException() {
            $this->File->getFileName(array('wrongType'));
        }

        /**
         * Test if ca content can be cached with an identifier.
         * @covers Brickoo\Cache\Provider\File::set
         */
        public function testSet() {
            $FileObjectStub = $this->getFileObjectStub(array());
            $FileObjectStub->expects($this->once())
                           ->method('setLocation')
                           ->will($this->returnSelf());
            $FileObjectStub->expects($this->once())
                           ->method('setMode')
                           ->will($this->returnSelf());
            $FileObjectStub->expects($this->exactly(2))
                           ->method('write')
                           ->will($this->returnSelf());
            $FileObjectStub->expects($this->once())
                           ->method('close')
                           ->will($this->returnSelf());

            $this->File->FileObject($FileObjectStub);

            $this->assertTrue($this->File->set('some identifier', 'content', 0));
        }

        /**
         * Test if a cached content can be retireved using the FileObject.
         * This test case uses the PHP temporary directory for the cache files.
         * @covers Brickoo\Cache\Provider\File::get
         */
        public function testGet() {
            file_put_contents
            (
                $this->File->getFilename('identifier'),
                (date(File::LIFETIME_FORMAT, time() + 999999)) . serialize('some cached content')
            );

            $this->assertEquals('some cached content', $this->File->get('identifier'));
        }

        /**
         * Test if trying to retrieve a not available cache file the check fails.
         * This test case uses the PHP temporary directory for the cache files.
         * @covers Brickoo\Cache\Provider\File::get
         */
        public function testGetFileDoesNotExist() {
            $this->assertFalse($this->File->get(uniqid()));
        }

        /**
         * Test if an expired file is not readed and it will be unlinked.
         * This test case uses the PHP temporary directory for the cache files.
         * @covers Brickoo\Cache\Provider\File::get
         * @covers Brickoo\Cache\Provider\File::delete
         */
        public function testGetFileExpired() {
            file_put_contents
            (
                $this->File->getFilename('identifier'),
                date(File::LIFETIME_FORMAT, (time() - 10)) . serialize('some cached content')
            );

            $this->assertFalse($this->File->get('identifier'));
        }

        /**
         * Test if trying to delete a not available cahe file it will return boolean false.
         * This test case uses the PHP temporary directory for the cache files.
         * @covers Brickoo\Cache\Provider\File::delete
         */
        public function testDeleteFileNotExists() {
            $this->assertFalse($this->File->delete('not_avaialble_identifier'));
        }

        /**
         * Test if the cache files can be unlinked from the cache directory.
         * This test case uses the PHP temporary directory for the cache files.
         * @covers Brickoo\Cache\Provider\File::flush
         */
        public function testFlush() {
            file_put_contents
            (
                $this->File->getFilename('identifier'),
                date(File::LIFETIME_FORMAT, (time() - 10)) . serialize('some cached content')
            );

            $this->assertInternalType('integer', $this->File->flush());
        }

    }