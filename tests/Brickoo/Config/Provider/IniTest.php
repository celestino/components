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

    namespace Tests\Brickoo\Config\Provider;

    use Brickoo\Config\Provider\Ini;

    /**
     * Test suite for the Ini provider class.
     * @see Brickoo\Config\Provider\Ini
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class IniProviderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Config\Provider\Ini::__construct
         */
        public function testConstructor() {
            $IniProvider = new Ini('test.ini');
            $this->assertInstanceOf('Brickoo\Config\Provider\Interfaces\Provider', $IniProvider);
            $this->assertAttributeEquals('test.ini', 'filename', $IniProvider);
        }

        /**
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
            $IniProvider = new Ini(dirname(__FILE__) .'/assets/test.ini');
            $this->assertEquals($expected, $IniProvider->load());
        }

        /**
         * @covers Brickoo\Config\Provider\Ini::load
         * @covers Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         */
        public function testLoadFileDoesNotExistThrowsException() {
            $IniProvider = new Ini('does_not_exists.ini');
            $IniProvider->load();
        }

        /**
         * @covers Brickoo\Config\Provider\Ini::load
         * @covers Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         */
        public function testLoadFileWithErrorSyntaxThrowsUnableToLoadConfigurationException() {
            $IniProvider = new Ini(dirname(__FILE__) .'/assets/test.error.ini');
            $IniProvider->load();
        }

        /**
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

            $IniProvider = new Ini('php://memory');
            $this->assertSame($IniProvider, $IniProvider->save($config));
        }

        /**
         * @covers Brickoo\Config\Provider\Ini::save
         * @covers Brickoo\Config\Provider\Exceptions\UnableToSaveConfiguration
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToSaveConfiguration
         */
        public function testSaveFileThrowsUnableToSaveConfigurationException() {
            $IniProvider = new Ini('/path/does/not/exist');
            $IniProvider->save(array('fails'));
        }

        /**
         * @covers Brickoo\Config\Provider\Ini::toString
         * @covers Brickoo\Config\Provider\Ini::getFlattenEntries
         */
        public function testToString() {
           $config = array(
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

            $IniProvider = new Ini("");
            $this->assertEquals($expected, $IniProvider->toString($config));
        }

    }