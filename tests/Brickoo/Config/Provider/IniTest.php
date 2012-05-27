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

    use Brickoo\Config\Provider\Ini;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Ini class.
     * @see Brickoo\Config\Provider\Ini
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class IniProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance  of the Ini class.
         * @var \Brickoo\Config\Provider\Ini
         */
        protected $Provider;

        /**
         * Set up the Provider instance used.
         * @return void
         */
        public function setUp() {
            $this->Provider = new Ini();
        }

        /**
         * Test if the filename properties is set.
         * @covers Brickoo\Config\Provider\Ini::__construct
         */
        public function testConstructor() {
            $Provider = new Ini('test.ini');
            $this->assertInstanceOf('Brickoo\Config\Provider\Interfaces\Provider', $Provider);
            $this->assertAttributeEquals('test.ini', 'filename', $Provider);
        }

        /**
         * Test if the filename can be set and retrieved.
         * @covers Brickoo\Config\Provider\Ini::getFilename
         * @covers Brickoo\Config\Provider\Ini::SetFilename
         */
        public function testGetSetFilename() {
            $expectedFilename = 'test.ini';
            $this->assertSame($this->Provider, $this->Provider->setFilename($expectedFilename));
            $this->assertAttributeEquals($expectedFilename, 'filename', $this->Provider);
            $this->assertEquals($expectedFilename, $this->Provider->getFilename());
        }

        /**
         * Test if trying to retrieve the not available filename throws an exception.
         * @covers Brickoo\Config\Provider\Ini::getFilename
         * @expectedException UnexpectedValueException
         */
        public function testGetFilenameValueException() {
            $this->Provider->getFilename();
        }

        /**
         * Test if an ini configuration can be loaded.
         * @covers Brickoo\Config\Provider\Ini::load
         */
        public function testLoad() {
            $expected = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                    'key3'    => array(1, 2, 3)
                )
            );
            $this->assertSame($this->Provider, $this->Provider->setFilename(dirname(__FILE__) .'/assets/test.ini'));
            $this->assertEquals($expected, $this->Provider->load());
        }

        /**
         * test if the file is not readable throws an exception.
         * @covers Brickoo\Config\Provider\Ini::load
         * @covers Brickoo\Config\Provider\Exceptions\UnableToLoadConfigurationException
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToLoadConfigurationException
         */
        public function testLoadFileException() {
            $this->Provider->setFilename('fail');
            $this->Provider->load();
        }

        /**
         * Test if the configuration can be saved to an ini file.
         * @covers Brickoo\Config\Provider\Ini::save
         */
        public function testSave() {
            $config = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                    'key3'    => array(1, 2, 3)
                )
            );

            $this->assertSame($this->Provider, $this->Provider->setFilename('php://memory'));
            $this->assertSame($this->Provider, $this->Provider->save($config));
        }

        /**
         * Test if trying to save to a not available file throws an exception.
         * @covers Brickoo\Config\Provider\Ini::save
         * @covers Brickoo\Config\Provider\Exceptions\UnableToSaveConfigurationException
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToSaveConfigurationException
         */
        public function testSaveFileException() {
            $this->Provider->setFilename('/path/does/not/exist');
            $this->Provider->save(array('fails'));
        }

        /**
         * Test if the configuration can be loaded from an ini formated string.
         * @covers Brickoo\Config\Provider\Ini::fromString
         */
        public function testFromString() {
            $expected = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                    'key3'    => array(1, 2, 3)
                )
            );

            $config = "[SECTION]\r\n".
                        "key1 = value1\r\n".
                        "key2 = \"value 2\"\r\n".
                        "key3[] = 1\r\n".
                        "key3[] = 2\r\n".
                        "key3[] = 3\r\n";

            $this->assertEquals($expected, $this->Provider->fromString($config));
        }

        /**
         * Test if the configuration can be converted to an ini string.
         * @covers Brickoo\Config\Provider\Ini::toString
         */
        public function testToString() {
           $config = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                    'key3'    => array(1, 2, 3)
                )
            );

            $expected = "[SECTION]\r\n".
                        "key1 = value1\r\n".
                        "key2 = \"value 2\"\r\n".
                        "key3[] = 1\r\n".
                        "key3[] = 2\r\n".
                        "key3[] = 3\r\n";

            $this->assertEquals($expected, $this->Provider->toString($config));
        }

        /**
         * Test if a section string can be created.
         * @covers Brickoo\Config\Provider\Ini::getFlattenEntries
         */
        public function testGetFlattenEntries() {
            $section = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                ),
                'key3'    => array(1, 2, 3),
                'key4'    => array('languages' => array('de', 'en'))
            );

            $expected = "[SECTION]\r\n".
                        "key1 = value1\r\n".
                        "key2 = \"value 2\"\r\n".
                        "[key3]\r\n".
                        "key3[] = 1\r\n".
                        "key3[] = 2\r\n".
                        "key3[] = 3\r\n".
                        "[key4]\r\n".
                        "languages[] = de\r\n".
                        "languages[] = en\r\n";

            $this->assertEquals($expected, $this->Provider->getFlattenEntries($section));
        }

    }