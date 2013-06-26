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

    namespace Tests\Brickoo\Config;

    use Brickoo\Config\Configuration;

    /**
     * Test suite for the Configuration class.
     * @see Brickoo\Core\Application
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ConfigurationTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Config\Configuration::__construct
         */
        public function testConstructor() {
            $Provider = $this->getMock('Brickoo\Config\Provider\Interfaces\Provider');
            $this->assertInstanceOf('Brickoo\Config\Interfaces\Configuration',
                ($Configuration = new Configuration($Provider))
            );
            $this->assertAttributeSame($Provider, 'Provider', $Configuration);
        }

        /**
         * @covers Brickoo\Config\Configuration::load
         */
        public function testLoad() {
            $expectedResult = array('Key' => 'Value');

            $Provider = $this->getMock('Brickoo\Config\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method('load')
                     ->will($this->returnValue($expectedResult));

            $Configuration = new Configuration($Provider);
            $this->assertSame($Configuration,  $Configuration->load());
            $this->assertAttributeEquals($expectedResult, 'container', $Configuration);
        }

        /**
         * @covers Brickoo\Config\Configuration::save
         */
        public function testSave() {
            $Provider = $this->getMock('Brickoo\Config\Provider\Interfaces\Provider');
            $Provider->expects($this->once())
                     ->method('save')
                     ->with(array('key' => 'value'))
                     ->will($this->returnSelf());

            $Configuration = new Configuration($Provider);
            $Configuration->fromArray(array('key' => 'value'));
            $this->assertSame($Configuration, $Configuration->save());
        }

        /**
         * @covers Brickoo\Config\Configuration::convertToConstants
         */
        public function testConvertSectionToConstants() {
            $config = array(
                'SECTION1' => array('key1' => 'value1')
            );
            $Provider = $this->getMock('Brickoo\Config\Provider\Interfaces\Provider');
            $Configuration = new Configuration($Provider);
            $Configuration->fromArray($config)
                          ->convertToConstants('SECTION1');

            $this->assertEquals('value1', SECTION1_KEY1);
        }

        /**
         * @covers Brickoo\Config\Configuration::convertToConstants
         * @expectedException UnexpectedValueException
         */
        public function testConvertionWithNotAvailableSectionThrowsException() {
            $Provider = $this->getMock('Brickoo\Config\Provider\Interfaces\Provider');
            $Configuration = new Configuration($Provider);
            $Configuration->convertToConstants('FAIL');
        }

        /**
         * @covers Brickoo\Config\Configuration::convertToConstants
         * @expectedException UnexpectedValueException
         */
        public function testConvertionWithNotScalarValuesThrowsException() {
            $config = array(
                'SECTION1' => array('key1' => array('wrongValueType'))
            );
            $Provider = $this->getMock('Brickoo\Config\Provider\Interfaces\Provider');
            $Configuration = new Configuration($Provider);
            $Configuration->fromArray($config);
            $Configuration->convertToConstants('SECTION1');
        }

    }