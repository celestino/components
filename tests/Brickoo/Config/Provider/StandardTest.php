<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Config\Provider;

    use Brickoo\Config\Provider\Standard;

    /**
     * Test suite for the Standard provider class.
     * @see Brickoo\Config\Provider\Standard
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class StandardTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Config\Provider\Standard::__construct
         */
        public function testConstructor() {
            $StandardProvider = new Standard('test.array.php');
            $this->assertInstanceOf('Brickoo\Config\Provider\Interfaces\Provider', $StandardProvider);
            $this->assertAttributeEquals('test.array.php', 'filename', $StandardProvider);
        }

        /**
         * @covers Brickoo\Config\Provider\Standard::load
         */
        public function testLoadConfiguration() {
            $expected = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                    'key3'    => array(1, 2, 3)
                )
            );
            $StandardProvider = new Standard(dirname(__FILE__) .'/assets/test.array.php');
            $this->assertEquals($expected, $StandardProvider->load());
        }

        /**
         * @covers Brickoo\Config\Provider\Standard::load
         * @covers Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         */
        public function testLoadFileException() {
            $StandardProvider = new Standard('file_does_not_exist.php');
            $StandardProvider->load();
        }

        /**
         * @covers Brickoo\Config\Provider\Standard::load
         * @covers Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToLoadConfiguration
         */
        public function testLoadEmptyValueThrowsException() {
            $StandardProvider = new Standard(dirname(__FILE__) .'/assets/test.empty.php');
            $StandardProvider->load();
        }

        /**
         * @covers Brickoo\Config\Provider\Standard::save
         */
        public function testSaveConfiguration() {
            $config = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                    'key3'    => array(1, 2, 3)
                )
            );

            $StandardProvider = new Standard('php://memory');
            $this->assertSame($StandardProvider, $StandardProvider->save($config));
        }

        /**
         * @covers Brickoo\Config\Provider\Standard::save
         * @covers Brickoo\Config\Provider\Exceptions\UnableToSaveConfiguration
         * @expectedException Brickoo\Config\Provider\Exceptions\UnableToSaveConfiguration
         */
        public function testSaveConfigurationThrowsException() {
            $StandardProvider = new Standard('/path/does/not/exist');
            $StandardProvider->save(array('fails'));
        }

        /**
         * @covers Brickoo\Config\Provider\Standard::toString
         */
        public function testToString() {
           $config = array(
                'SECTION' => array(
                    'key1'    => 'value1',
                    'key2'    => 'value 2',
                    'key3'    => array(1, 2, 3)
                )
            );

            $expected = "<?php \r\nreturn ". var_export($config, true) .";";

            $StandardProvider = new Standard("");
            $this->assertEquals($expected, $StandardProvider->toString($config));
        }

    }