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
     * @version $Id: CliRequestTest.php 15 2011-12-23 02:05:32Z celestino $
     */

    class CliRequestTest extends PHPUnit_Framework_TestCase
    {

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
            $_SERVER['argc'] = 3;
            $_SERVER['argv'] = array('test.php', 'ARG1', 'ARG2', 'ARG3');

            $this->Cli = new Request(new Core\Request());
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Cli\Request::__construct
         * @covers Brickoo\Library\Cli\Request::clear
         */
        public function testCliConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Cli\Interfaces\RequestInterface',
                $this->Cli
            );
        }

        /**
         * Test if the cli arguemnts can be retrieved.
         * @covers Brickoo\Library\Cli\Request::getArguments
         * @covers Brickoo\Library\Cli\Request::collectArguments
         */
         public function testGetArguments()
        {
            $this->assertInternalType('array', $this->Cli->getArguments());
            $this->assertContainsOnly('string', $this->Cli->getArguments());
        }

        /**
         * Test if a cli argument can be retrieved.
         * @covers Brickoo\Library\Cli\Request::getArgument
         */
        public function testGetArgument()
        {
            $this->assertEquals('test.php', $this->Cli->getArgument(0));
            $this->assertEquals('ARG1', $this->Cli->getArgument(1));
            $this->assertEquals('ARG2', $this->Cli->getArgument(2));
            $this->assertEquals('ARG3', $this->Cli->getArgument(3));
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
         * Test if keys can be binded to the arguments and return the object reference.
         * @covers Brickoo\Library\Cli\Request::setArgumentsKeys
         */
        public function testSetArgumentsKeys()
        {
            $this->assertSame($this->Cli, $this->Cli->setArgumentsKeys(array('file', 'v1', 'v2', 'v3', 'x')));
            $this->assertEquals('test.php', $this->Cli->getArgument('file'));
            $this->assertEquals('ARG1', $this->Cli->getArgument('v1'));
            $this->assertEquals('ARG2', $this->Cli->getArgument('v2'));
            $this->assertEquals('ARG3', $this->Cli->getArgument('v3'));
            $this->assertNull($this->Cli->getArgument('v4'));
        }

        /**
         * Test if it returns the number of arguments passed.
         * @covers Brickoo\Library\Cli\Request::countArguments
         */
        public function testCountArguments()
        {
            $this->assertEquals(4, $this->Cli->countArguments());
        }

        /**
         * Test if it recognied available cli arguments.
         * @covers Brickoo\Library\Cli\Request::hasArguments
         */
        public function testHasArguments()
        {
            $this->assertTrue($this->Cli->hasArguments());
        }

    }

?>