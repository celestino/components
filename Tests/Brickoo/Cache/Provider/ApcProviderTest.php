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

    use Brickoo\Library\Cache\Provider\APCProvider;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * ApcProviderTest
     *
     * Test suite for the APCProvider class.
     * @see Brickoo\Library\Cache\Provider\APCProvider
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApcProviderTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the ApcProvider class.
         * @var ApcProvider
         */
        protected $ApcProvider;

        /**
         * Set up the ApcProvider object used.
         * @return void
         */
        protected function setUp()
        {
            if
            (
                (! extension_loaded('apc')) ||
                (! ini_get('apc.enable_cli'))
            )
            {
                $this->markTestSkipped('The apc extension is not available.');
            }

            $this->ApcProvider = new ApcProvider();
        }

        /**
         * Test if a content can be set to the Apc and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::set
         */
        public function testSet()
        {
            $this->assertTrue($this->ApcProvider->set('some_identifier', 'some cached content', 3600));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::set
         * @expectedException InvalidArgumentException
         */
        public function testSetArgumentException()
        {
            $this->ApcProvider->set(array('wrongType'), 'whatever', array('wrongType'));
        }

        /**
         * Test if a content can be retrieved from the APC.
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::get
         * @depends testSet
         */
        public function testGet()
        {
            $this->assertEquals('some cached content', $this->ApcProvider->get('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException()
        {
            $this->ApcProvider->get(array('wrongType'));
        }

        /**
         * Test if a cached content can be delete by its identifier and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::delete
         * @depends testSet
         */
        public function testDelete()
        {
            $this->assertTrue($this->ApcProvider->delete('some_identifier'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::delete
         * @expectedException InvalidArgumentException
         */
        public function testDeleteArgumentException()
        {
            $this->ApcProvider->delete(array('wrongType'));
        }

        /**
         * Test if a cached content can be flushed and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::flush
         */
        public function testFlush()
        {
            $this->assertTrue($this->ApcProvider->flush());
        }

        /**
         * Test if a APC method not implemented can be called and the result is returned.
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::__call
         */
        public function test__call()
        {
            $this->assertTrue($this->ApcProvider->apc_add('some_identifier', 'some other content', 3600));
            $this->assertEquals('some other content', $this->ApcProvider->get('some_identifier'));
        }

        /**
         * Test if trying to call a not available APC function throws an exception
         * @covers Brickoo\Library\Cache\Provider\ApcProvider::__call
         * @expectedException BadMethodCallException
         */
        public function test__callBadMethodCallException()
        {
            $this->ApcProvider->whatever();
        }

    }
