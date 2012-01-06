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

    use Brickoo\Library\Core\Request;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Core Request class.
     * @see Brickoo\Library\Core\Request
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RequestTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Request object.
         * @var object
         */
        protected $Request;

        /**
         * Set up the Request object used.
         */
        public function setUp()
        {
            $_SERVER['REQUEST_METHOD'] = 'GET';

            $this->Request = new Request();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Core\Request::__construct
         */
        public function testRequestConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Core\Interfaces\RequestInterface',
                $this->Request
            );
        }

        /**
         * Test if the reset method returns the object reference.
         * @covers Brickoo\Library\Core\Request::reset
         */
        public function testClear()
        {
            $this->assertSame($this->Request, $this->Request->reset());
        }

        /**
         * Test if a server value can be retrieved and the default value is returned.
         * @covers Brickoo\Library\Core\Request::getServerVar
         * @covers Brickoo\Library\Core\Request::collectServerVars
         */
        public function testGetServerValue()
        {
            $this->assertNull($this->Request->getServerVar('does.not.exists'));
            $this->assertEquals('GET', $this->Request->getServerVar('request.method'));
            $this->assertEquals('DEFAULT', $this->Request->getServerVar('NOTHING', 'DEFAULT'));
        }

        /**
         * Test if the interface of PHP is recognized.
         * @covers Brickoo\Library\Core\Request::isPHPInterface
         */
        public function testIsPHPInterface()
        {
            $this->assertTrue($this->Request->isPHPInterface('cli'));
        }

        /**
         * Test if an not valid argument type throw an Exception.
         * @covers Brickoo\Library\Core\Request::isPHPInterface
         * @expectedException InvalidArgumentException
         */
        public function testIsPHPInterfaceArgumentException()
        {
            $this->Request->isPHPInterface(true);
        }

    }

?>