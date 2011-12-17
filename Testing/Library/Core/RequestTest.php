<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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
     * Test case for the Core Request class.
     * @see Brickoo\Library\Core\Request
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class RequestTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Requestobject.
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
         * @covers Brickoo\Library\Core\Interfaces\RequestInterface
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
         * Test if the clear method returns the object reference.
         * @covers Brickoo\Library\Core\Request::clear
         */
        public function testClear()
        {
            $this->assertSame($this->Request, $this->Request->clear());
        }

        /**
         * Test if a server value can be retrieved.
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

        /**
         * Test if the Http Request object is returned.
         * @covers Brickoo\Library\Core\Request::Http
         * @covers Brickoo\Library\Core\Request::addHttpSupport
         * @covers Brickoo\Library\Http\Interfaces\HttpRequestInterface
         */
        public function testGetHttpObject()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Http\Interfaces\HttpRequestInterface',
                $this->Request->Http()
            );
        }

        /**
         * Test if the Http support is availabe.
         * @covers Brickoo\Library\Core\Request::addHttpSupport
         * @covers Brickoo\Library\Http\Interfaces\HttpRequestInterface
         */
        public function testAddHttpSupport()
        {
            $this->assertSame($this->Request, $this->Request->addHttpSupport());
        }

        /**
         * Test if the Http support is availabe through passed reference.
         * @covers Brickoo\Library\Core\Request::addHttpSupport
         */
        public function testAddHttpSupportPassed()
        {
            $HttpRequest = new \Brickoo\Library\Http\Request($this->Request);
            $this->assertSame($this->Request, $this->Request->addHttpSupport($HttpRequest));
        }

        /**
         * Test if the reassigning an Http instance throws an exception.
         * @covers Brickoo\Library\Core\Request::addHttpSupport
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverrideException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverrideException
         */
        public function testAddHttpSupportDependencException()
        {
            $this->assertSame($this->Request,$this->Request->addHttpSupport());

            $HttpRequest = new \Brickoo\Library\Http\Request($this->Request);
            $this->assertSame($this->Request, $this->Request->addHttpSupport($HttpRequest));
        }

        /**
         * Test if the Cli Request object is returned.
         * @covers Brickoo\Library\Core\Request::Cli
         * @covers Brickoo\Library\Cli\Interfaces\CliRequestInterface
         */
        public function testGetCliObject()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Cli\Interfaces\CliRequestInterface',
                $this->Request->Cli()
            );
        }

        /**
         * Test if the Cli support is availabe.
         * @covers Brickoo\Library\Core\Request::addCliSupport
         * @covers Brickoo\Library\Cli\Interfaces\CliRequestInterface
         */
        public function testAddCliSupport()
        {
            $this->assertSame($this->Request, $this->Request->addCliSupport());
        }

        /**
         * Test if the Cli support is availabe through passed reference.
         * @covers Brickoo\Library\Core\Request::addCliSupport
         */
        public function testAddCliSupportPassed()
        {
            $Cli = new \Brickoo\Library\Cli\Request($this->Request);
            $this->assertSame($this->Request, $this->Request->addCliSupport($Cli));
        }

        /**
         * Test if the reassigning an Cli instance throws an exception.
         * @covers Brickoo\Library\Core\Request::addCliSupport
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverrideException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverrideException
         */
        public function testAddCliSupportDependencyException()
        {
            $this->assertSame($this->Request, $this->Request->addCliSupport());

            $Cli = new \Brickoo\Library\Cli\Request($this->Request);
            $this->assertSame($this->Request, $this->Request->addCliSupport($Cli));
        }

    }

?>