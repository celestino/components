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

    use Brickoo\Log\Handler\FileHandler;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * FileHandlerTest
     *
     * Test suite for the FileHandler class.
     * @see Brickoo\Log\Handler\FileHandler
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileHandlerTest extends PHPUnit_Framework_TestCase {

        /**
        * Holds an instance of the FileHandler object.
        * @var object
        */
        protected $FileHandler;

        /**
         * Set up the FileHandler object used.
         */
        public function setUp() {
            $this->FileHandler = new FileHandler($this->getFileObjectStub());
        }

        /**
         * Returns an Stub of the FileObject class.
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
        * Test if the class can be created and implementens the LogHandler.
        * @covers Brickoo\Log\handler\FileHandler::__construct
        */
        public function testConstructor() {
            $FileObject = $this->getFileObjectStub();
            $this->assertInstanceOf
            (
                '\Brickoo\Log\Handler\Interfaces\LogHandler',
                ($FileHandler = new FileHandler($FileObject))
            );
            $this->assertAttributeSame($FileObject, '_FileObject', $FileHandler);
        }

        /**
         * Test if the FileObject can be retrieved and implements te FileObject.
         * @covers Brickoo\Log\Handler\FileHandler::FileObject
         */
        public function testGetFileObject() {
            $this->assertInstanceOf
            (
                '\Brickoo\System\Interfaces\FileObject',
                $this->FileHandler->FileObject()
            );
        }

        /**
         * Test if the directory can be set and returns the FileHandler reference.
         * @covers Brickoo\Log\Handler\FileHandler::setDirectory
         */
        public function testSetDirectory() {
            $this->assertSame($this->FileHandler, $this->FileHandler->setDirectory('/var/www/log/'));
        }

        /**
         * Test if the passing wront argument tyype as directory throw an exception.
         * @covers Brickoo\Log\Handler\FileHandler::setDirectory
         * @expectedException InvalidArgumentException
         */
        public function testSetDirectoryArgumentException() {
            $this->assertSame($this->FileHandler, $this->FileHandler->setDirectory(array('wrongTyoe')));
        }

        /**
         * Test if the directory can be retrieved and the last backslash/slash is removed.
         * @covers Brickoo\Log\Handler\FileHandler::getDirectory
         */
        public function testGetDirectory() {
            $this->FileHandler->setDirectory('/var/log/');
            $this->assertEquals('/var/log' . DIRECTORY_SEPARATOR, $this->FileHandler->getDirectory());
        }

        /**
         * Test if the retirving the not available directorythrows an exception.
         * @covers Brickoo\Log\Handler\FileHandler::getDirectory
         * @expectedException UnexpectedValueException
         */
        public function testGetDirectoryUnexpectedValueException() {
           $this->FileHandler->getDirectory();
        }

        /**
         * Test if the FilePrefix can be set and the FileHandler reference is returned.
         * @covers \Brickoo\Log\Handler\FileHandler::setFilePrefix
         */
        public function testSetFilePrefix() {
            $this->assertSame($this->FileHandler, $this->FileHandler->setFilePrefix('test_'));
        }

        /**
         * Test if a wrong argument type throws an exception.
         * @covers \Brickoo\Log\Handler\FileHandler::setFilePrefix
         * @expectedException InvalidArgumentException
         */
        public function testSetFilePrefixArgumentException() {
            $this->FileHandler->setFilePrefix(array('wrongType'));
        }

        /**
         * Test if the FilePrefix can be retrived.
         * @covers \Brickoo\Log\Handler\FileHandler::getFilePrefix
         */
        public function testGetFilePrefix() {
            $this->assertEquals('log_', $this->FileHandler->getFilePrefix());
            $this->FileHandler->setFilePrefix('test_');
            $this->assertEquals('test_', $this->FileHandler->getFilePrefix());
        }

        /**
         * Test if the messages array can be converted to a single message.
         * Test if the default severiy `Debug`is used if the severity is unknowed.
         * @covers \Brickoo\Log\Handler\FileHandler::convertToLogMessage
         */
        public function testConvertToLogMessage() {
            $messages = array('message 1', 'message 2');
            $severity = 6;

            $expected = '['. date("Y-m-d H:i:s") . '][Info] message 1' . PHP_EOL .
                        '['. date("Y-m-d H:i:s") . '][Info] message 2' . PHP_EOL;
            $this->assertEquals($expected, $this->FileHandler->convertToLogMessage($messages, $severity));

            $expected = '['. date("Y-m-d H:i:s") . '][Debug] message 1' . PHP_EOL .
                        '['. date("Y-m-d H:i:s") . '][Debug] message 2' . PHP_EOL;
            $this->assertEquals($expected, $this->FileHandler->convertToLogMessage($messages, 123));
        }

        /**
         * Test if an empty message array throws an exception.
         * @covers \Brickoo\Log\Handler\FileHandler::convertToLogMessage
         * @expectedException InvalidArgumentException
         */
        public function testConvertToLogMessageArgumentMessageException() {
            $this->FileHandler->convertToLogMessage(array(), 7);
        }

        /**
         * Test if a wrong severity argument type throws an exception.
         * @covers \Brickoo\Log\Handler\FileHandler::convertToLogMessage
         * @expectedException InvalidArgumentException
         */
        public function testConvertToLogMessageArgumentSeverityException() {
            $this->FileHandler->convertToLogMessage(array('message'), 'wrongType');
        }

        /**
        * Test if messages could be logged.
        * @covers \Brickoo\Log\Handler\FileHandler::log
        */
        public function testlog() {
            date_default_timezone_set('UTC');

            $FileObject = $this->getFileObjectStub(array('setMode', 'setLocation', 'write', 'close'));

            $FileObject->expects($this->once())
                       ->method('setMode')
                       ->will($this->returnSelf());
            $FileObject->expects($this->once())
                       ->method('setLocation')
                       ->will($this->returnSelf());
            $FileObject->expects($this->once())
                       ->method('write')
                       ->will($this->returnSelf());
            $FileObject->expects($this->once())
                       ->method('close')
                       ->will($this->returnSelf());

            $FileHandler = new FileHandler($FileObject);
            $FileHandler->setDirectory('/var/log/');
            $this->assertSame($FileHandler, $FileHandler->log('mesage', 7));
        }

        /**
        * Test if wrong severity argument type throws an exception..
        * @covers \Brickoo\Log\Handler\FileHandler::log
        * @expectedException InvalidArgumentException
        */
        public function testlogArgumentException() {
            $this->FileHandler->log('message', 'wrongType');
        }

    }