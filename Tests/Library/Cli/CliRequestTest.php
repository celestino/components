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

    use Brickoo\Library\Core;
    use Brickoo\Library\Cli\Request;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * CliRequestTest
     *
     * Test suite for the Cli Request class.
     * @see Brickoo\Library\Cli\Request
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CliRequestTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns an Core Request Stub for the Http Request to look up for server variables.
         * @return object Request stub
         */
        protected function getRequestStub()
        {
            return $this->getMock('Brickoo\Library\Core\Request', array('getServerVar'));
        }

        /**
         * Holds an instance of the Cli class.
         * @var object
         */
        public $Cli;

        /**
         * Set up the Cli object used.
         * @return void
         */
        public function setUp()
        {
            $this->Cli = new Request();
        }

        /**
         * Test if the Core\request can be injected.
         * @covers Brickoo\Library\Cli\Request::__construct
         * @covers Brickoo\Library\Cli\Request::reset
         */
        public function testCliConstructor()
        {
            $CoreRequestStub = $this->getRequestStub();
            $this->Cli->injectCoreRequest($CoreRequestStub);
            $this->assertAttributeSame($CoreRequestStub, 'CoreRequest', $this->Cli);
        }

        /**
         * Test if the Core\Request dependency can be injectd.
         * @covers Brickoo\Library\Cli\Request::injectCoreRequest
         */
        public function testInjectCoreRequest()
        {
            $CoreRequestStub = $this->getRequestStub();
            $this->assertSame($this->Cli, $this->Cli->injectCoreRequest($CoreRequestStub));
            $this->assertAttributeSame($CoreRequestStub, 'CoreRequest', $this->Cli);

            return $this->Cli;
        }

        /**
         * Test if trying to overwrite the Core\Request dependency throws an expcetion.
         * @covers Brickoo\Library\Cli\Request::injectCoreRequest
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @depends testInjectCoreRequest
         */
        public function testInjectCoreRequestOverwriteException($Cli)
        {
            $Cli->injectCoreRequest($this->getRequestStub());
        }

        /**
         * Test if the Core\Request dependency can be retrieved.
         * @covers Brickoo\Library\Cli\Request::getCoreRequest
         */
        public function testGetCoreRequest()
        {
            $CoreRequestStub = $this->getRequestStub();
            $this->Cli->injectCoreRequest($CoreRequestStub);
            $this->assertSame($CoreRequestStub, $this->Cli->getCoreRequest());
        }

        /**
         * Test if the Core\Request can be lazy initializated.
         * @covers Brickoo\Library\Cli\Request::getCoreRequest
         */
        public function testGetRequestLazy()
        {
            $this->Cli = new Request();
            $this->assertInstanceOf('Brickoo\Library\Core\Interfaces\RequestInterface', $this->Cli->getCoreRequest());
        }

        /**
         * Test if keys can be binded to the arguments and returns the object reference.
         * @covers Brickoo\Library\Cli\Request::setArgumentsKeys
         */
        public function testSetArgumentsKeys()
        {
            $valueMap = array(array('argv', null, array('test.php', 'ARG1', 'ARG2', 'ARG3')));

            $CoreRequestStub = $this->getRequestStub();
            $CoreRequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValueMap($valueMap));

            $this->Cli->injectCoreRequest($CoreRequestStub);

            $this->assertSame($this->Cli, $this->Cli->setArgumentsKeys(array('file', 'v1', 'v2', 'v3', 'default')));
        }

        /**
         * Test if the cli arguemnts can be retrieved.
         * @covers Brickoo\Library\Cli\Request::getArguments
         * @covers Brickoo\Library\Cli\Request::collectArguments
         */
         public function testGetArguments()
        {
            $valueMap = array(array('argv', null, array('test.php', 'ARG1', 'ARG2', 'ARG3')));

            $CoreRequestStub = $this->getRequestStub();
            $CoreRequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValueMap($valueMap));

            $this->Cli->injectCoreRequest($CoreRequestStub);

           $this->assertEquals(array('test.php', 'ARG1', 'ARG2', 'ARG3'), $this->Cli->getArguments());
        }

        /**
         * Test if a cli argument can be retrieved.
         * @covers Brickoo\Library\Cli\Request::getArgument
         */
        public function testGetArgument()
        {
            $valueMap = array(array('argv', null, array('test.php')));

            $CoreRequestStub = $this->getRequestStub();
            $CoreRequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValueMap($valueMap));

            $this->Cli->injectCoreRequest($CoreRequestStub);

            $this->assertEquals('test.php', $this->Cli->getArgument(0));
            $this->assertEquals('DEFAULT', $this->Cli->getArgument(4, 'DEFAULT'));
        }

        /**
         * Test if a wrong cli argument type throws an exception.
         * @covers Brickoo\Library\Cli\Request::getArgument
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException()
        {
            $this->Cli->getArgument(array('wrongType'));
        }

        /**
         * Test if it returns the number of arguments passed.
         * @covers Brickoo\Library\Cli\Request::countArguments
         */
        public function testCountArguments()
        {
            $valueMap = array(array('argv', null, array('test.php', 'ARG1', 'ARG2')));

            $CoreRequestStub = $this->getRequestStub();
            $CoreRequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValueMap($valueMap));

            $this->Cli->injectCoreRequest($CoreRequestStub);

            $this->assertEquals(3, $this->Cli->countArguments());
        }

        /**
         * Test if it recognied available cli arguments.
         * @covers Brickoo\Library\Cli\Request::hasArguments
         */
        public function testHasArguments()
        {
            $valueMap = array(array('argv', null, array('test.php', 'ARG1', 'ARG2')));

            $CoreRequestStub = $this->getRequestStub();
            $CoreRequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValueMap($valueMap));

            $this->Cli->injectCoreRequest($CoreRequestStub);

            $this->assertTrue($this->Cli->hasArguments());
        }

        /**
         * Test if the request path can be set and the Cli reference is returned.
         * @covers Brickoo\Library\Cli\Request::setRequestPath
         * @return object the used Cli instance
         */
        public function testSetRequestPath()
        {
            $this->assertSame($this->Cli, $this->Cli->setRequestPath('/path/used'));
            $this->assertAttributeEquals('/path/used', 'requestPath', $this->Cli);

            return $this->Cli;
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Cli\Request::setRequestPath
         * @expectedException InvalidArgumentException
         */
        public function testSetRequestPathArgumentException()
        {
            $this->Cli->setRequestPath(array('wrongType'));
        }

        /**
         * Test if the request path can be retrieved.
         * @param Brickoo\Library\Cli\Request $this->Cli the Cli instance
         * @covers Brickoo\Library\Cli\Request::getRequestPath
         * @depends testSetRequestPath
         */
        public function testGetRequestPath($Cli)
        {
            $this->assertEquals('/path/used', $Cli->getRequestPath());
        }

        /**
         * Test if trying to retrieve the not set request path throws an exception.
         * @covers Brickoo\Library\Cli\Request::getRequestPath
         * @expectedException UnexpectedValueException
         */
        public function testGetRequestPathValueException()
        {
            $this->Cli->getRequestPath();
        }

        /**
         * Test if the request method can be retrieved.
         * @covers Brickoo\Library\Cli\Request::getRequestMethod
         */
        public function testGetRequestMethod()
        {
            $this->assertEquals('LOCAL', $this->Cli->getRequestMethod());
        }

    }

?>