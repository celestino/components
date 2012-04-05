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

    use Brickoo\Http\Application;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * HttpApplicationTest
     *
     * Test suite for the Application class.
     * @see Brickoo\Http\Application
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HttpApplicationTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Holds an instance of the Application class.
         * @var \Brickoo\Http\Application
         */
        protected $Application;

        /**
         * Sets up the Application instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->Application = new Application();
        }

        /**
         * Test if the Application listeners can be aggregaded and the flag is set.
         * @covers Brickoo\Http\Application::aggregateListeners
         * @covers Brickoo\Core\Events
         * @covers Brickoo\Module\Events
         */
        public function testAggregateListeners()
        {
            $EventManager = $this->getMock('Brickoo\Event\Manager', array('addListener'));
            $this->assertAttributeEquals(false, 'listenerAggregated', $this->Application);
            $this->assertNull($this->Application->aggregateListeners($EventManager));
            $this->assertAttributeEquals(true, 'listenerAggregated', $this->Application);

        }

        /**
         * Test if the Response can be lazy initialized.
         * @covers Brickoo\Http\Application::Response
         * @covers Brickoo\Http\Application::getDependency
         */
        public function testResponseLazyInitialization()
        {
            $this->assertInstanceOf('Brickoo\Core\Interfaces\ResponseInterface', $this->Application->Response());
        }

        /**
         * Test if the error Response content is set and and will be sent.
         * @covers Brickoo\Http\Application::displayError
         * @covers Brickoo\Http\Application::Response
         * @covers Brickoo\Http\Application::getDependency
         */
        public function testDisplayError()
        {
            $Exception = new \Exception();

            $Response = $this->getMock('Brickoo\Http\Response', array('setContent', 'send'));
            $Response->expects($this->once())
                     ->method('setContent');
            $Response->expects($this->once())
                     ->method('send');

            $this->Application->Response($Response);

            $this->assertNull($this->Application->displayError($Exception));
        }

        /**
         * Test if the module error could be trasformed into a http response.
         * @covers Brickoo\Http\Application::displayModuleError
         */
        public function testDisplayModuleError()
        {
            $Exception = new \Exception();

            $Response = $this->getMock('Brickoo\Http\Response', array('setContent'));
            $Response->expects($this->once())
                     ->method('setContent');
            $this->Application->Response($Response);

            $this->assertSame($Response, $this->Application->displayModuleError($Exception));
        }

        /**
         * Test if the error Response content is set and and will be sent.
         * @covers Brickoo\Http\Application::displayResponseError
         * @covers Brickoo\Http\Application::Response
         * @covers Brickoo\Http\Application::getDependency
         */
        public function testDisplayResponseError()
        {
            $Event = $this->getMock('Brickoo\Event\Event', null, array('test.event'));

            $Response = $this->getMock('Brickoo\Http\Response', array('setContent', 'send'));
            $Response->expects($this->once())
                     ->method('setContent');
            $Response->expects($this->once())
                     ->method('send');

            $this->Application->Response($Response);

            $this->assertNull($this->Application->displayResponseError($Event));
        }

        /**
         * Test if the Response is returned after processing the controller call.
         * @covers Brickoo\Http\Application::run
         * @covers Brickoo\Module\Events
         */
        public function testRun()
        {
            include (dirname(__FILE__) .'/Assets/TestController.php');

            $EventManager = $this->getMock('Brickoo\Event\Manager', array('notify'));
            $EventManager->expects($this->any())
                         ->method('notify');

            $Route = $this->getMock('Brickoo\Routing\Route', array('getController'), array('test.route'));
            $Route->expects($this->once())
                  ->method('getController')
                  ->will($this->returnValue(array(
                      'controller'    => 'TestController',
                      'method'        => 'testMethod',
                      'static'        => false
                  )));

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', array('getModuleRoute'), array($Route));
            $RequestRoute->expects($this->once())
                         ->method('getModuleRoute')
                         ->will($this->returnValue($Route));

            $Event = $this->getMock('Brickoo\Event\Event', array('getParam', 'EventManager'), array('response.get'));
            $Event->expects($this->once())
                  ->method('getParam')
                  ->with('Route')
                  ->will($this->returnValue($RequestRoute));
            $Event->expects($this->any())
                  ->method('EventManager')
                  ->will($this->returnValue($EventManager));

            $this->assertEquals('test response.', $this->Application->run($Event));
        }

        /**
         * Test if the Response is returned after processing the static controller call.
         * @covers Brickoo\Http\Application::run
         * @covers Brickoo\Module\Events
         */
        public function testRunStaticController()
        {
            include (dirname(__FILE__) .'/Assets/TestStaticController.php');

            $EventManager = $this->getMock('Brickoo\Event\Manager', array('notify'));
            $EventManager->expects($this->any())
                         ->method('notify');

            $Route = $this->getMock('Brickoo\Routing\Route', array('getController'), array('test.route'));
            $Route->expects($this->once())
                  ->method('getController')
                  ->will($this->returnValue(array(
                      'controller'    => 'TestStaticController',
                      'method'        => 'TestMethod',
                      'static'        => true
                  )));

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', array('getModuleRoute'), array($Route));
            $RequestRoute->expects($this->once())
                         ->method('getModuleRoute')
                         ->will($this->returnValue($Route));

            $Event = $this->getMock('Brickoo\Event\Event', array('getParam', 'EventManager'), array('response.get'));
            $Event->expects($this->once())
                  ->method('getParam')
                  ->with('Route')
                  ->will($this->returnValue($RequestRoute));
            $Event->expects($this->any())
                  ->method('EventManager')
                  ->will($this->returnValue($EventManager));

            $this->assertEquals('test static response.', $this->Application->run($Event));
        }

        /**
        * Test if the an error occured an excetion is throwed.
        * @covers Brickoo\Http\Application::run
        */
        public function testControllerException()
        {
            $Route = $this->getMock('Brickoo\Routing\Route', array('getController'), array('test.route'));
            $Route->expects($this->once())
                  ->method('getController')
                  ->will($this->returnValue(array(
                        'controller'    => 'TestController',
                        'method'        => 'exceptionMethod',
                        'static'        => false
                  )));

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', array('getModuleRoute'), array($Route));
            $RequestRoute->expects($this->once())
                         ->method('getModuleRoute')
                         ->will($this->returnValue($Route));

            $Event = $this->getMock('Brickoo\Event\Event', array('getParam'), array('response.get'));
            $Event->expects($this->once())
                  ->method('getParam')
                  ->with('Route')
                  ->will($this->returnValue($RequestRoute));

            $this->Application->run($Event);
        }

        /**
         * Test if the response would be sent.
         * @covers Brickoo\Http\Application::sendResponse
         */
        public function testSendResponse()
        {
            $Response = $this->getMock('Brickoo\Http\Response', array('send'));
            $Response->expects($this->once())
                     ->method('send');

            $this->assertSame($this->Application, $this->Application->sendResponse($Response));
        }

    }