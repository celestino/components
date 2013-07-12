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

    namespace Tests\Brickoo\Routing\Route;

    use Brickoo\Routing\Route\Executable;

    /**
     * ExecutableTest
     *
     * Test suite for the Executable class.
     * @see Brickoo\Routing\Route\Executable
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ExecutableTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Route\Executable::__construct
         */
        public function testConstructor() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $parameters = array('name' => 'test');
            $Executable = new Executable($Route, $parameters);
            $this->assertInstanceOf('Brickoo\Routing\Route\Interfaces\Executable', $Executable);
            $this->assertAttributeSame($Route, 'Route', $Executable);
            $this->assertAttributeSame($parameters, 'parameters', $Executable);
            $this->assertAttributeEquals(false, "hasBeenExecuted", $Executable);
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::getRoute
         */
        public function testGetRoute() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Executable = new Executable($Route);
            $this->assertSame($Route, $Executable->getRoute());
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::getParameters
         */
        public function testGetParameters() {
            $expectedParameters = array('param' => 'the parameter value');

            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Executable = new Executable($Route, $expectedParameters);
            $this->assertEquals($expectedParameters, $Executable->getParameters());
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::getParameter
         */
        public function testGetParameter() {
            $parameters = array('param' => 'the parameter value');

            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Executable = new Executable($Route, $parameters);
            $this->assertEquals($parameters['param'], $Executable->getParameter('param'));
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::getParameter
         * @expectedException InvalidArgumentException
         */
        public function testGetParameterThrowsInvalidArgumentException() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Executable = new Executable($Route);
            $Executable->getParameter(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::getParameter
         * @covers Brickoo\Routing\Route\Exceptions\ParameterNotAvailable
         * @expectedException Brickoo\Routing\Route\Exceptions\ParameterNotAvailable
         */
        public function testGetParameterThrowsParameterNotAvailableException() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Executable = new Executable($Route);
            $Executable->getParameter('not.available');
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::hasParameter
         */
        public function testHasParameter() {
            $parameters = array('param' => 'the parameter value');

            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Executable = new Executable($Route, $parameters);
            $this->assertFalse($Executable->hasParameter('nots.available'));
            $this->assertTrue($Executable->hasParameter('param'));
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::hasParameter
         * @expectedException InvalidArgumentException
         */
        public function testHasParameterThrowsInvalidArgumentException() {
            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Executable = new Executable($Route);
            $Executable->hasParameter(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::execute
         */
        public function testExecute() {
            require_once "Assets/ExecutableController.php";

            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Route->expects($this->once())
                  ->method('getController')
                  ->will($this->returnValue('\Tests\Brickoo\Routing\Route\Assets\ExecutableController'));
            $Route->expects($this->once())
                  ->method('getAction')
                  ->will($this->returnValue('returnText'));

            $Executable = new Executable($Route);
            $this->assertEquals("ExecutableController::returnText executed.", $Executable->execute());
        }

        /**
         * @covers Brickoo\Routing\Route\Executable::execute
         * @covers \Brickoo\Routing\Route\Exceptions\MultipleExecutions
         * @expectedException \Brickoo\Routing\Route\Exceptions\MultipleExecutions
         */
        public function testExecuteThrowsMultipleExecutionsException() {
            require_once "Assets/ExecutableController.php";

            $Route = $this->getMock('Brickoo\Routing\Route\Interfaces\Route');
            $Route->expects($this->once())
                  ->method('getController')
                  ->will($this->returnValue('\Tests\Brickoo\Routing\Route\Assets\ExecutableController'));
            $Route->expects($this->once())
                  ->method('getAction')
                  ->will($this->returnValue('returnText'));

            $Executable = new Executable($Route);
            $Executable->execute();
            $this->assertAttributeEquals(true, "hasBeenExecuted", $Executable);
            $Executable->execute();

        }

    }