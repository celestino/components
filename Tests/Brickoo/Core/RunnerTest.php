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

    use Brickoo\Core\Runner;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Runner class.
     * @see Brickoo\Core\Runner
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RunnerTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Runner class.
         * @var \Brickoo\Core\Runner
         */
        protected $Runner;

        /**
         * Sets up the Runner instance used.
         * @return void
         */
        protected function setUp() {
            $this->Runner = new Runner();
        }

        /**
         * Test if the listeners are aggreagted and attached to the EventManager.
         * @covers Brickoo\Core\Runner::aggregateListeners
         */
        public function testAggregateListeners() {
            $EventManager = $this->getMock('Brickoo\Event\Manager');
            $EventManager->expects($this->exactly(5))
                         ->method('attachListener')
                         ->with($this->isType('string'), $this->isType('array'), $this->equalTo(100),
                             $this->isNull(), $this->isInstanceOf('Closure')
                         );

            $this->assertNull($this->Runner->aggregateListeners($EventManager));
        }

        /**
         * Test if the listener does boot the router.
         * @covers Brickoo\Core\Runner::boot
         */
        public function testBoot() {
            $modules = array('moduleA' => '/path/to/moduleA');

            $Router = $this->getMock(
                'Brickoo\Routing\Router', array('hasModules', 'setModules'), array($this->getMock('Brickoo\Http\Request'))
            );
            $Router->expects($this->once())
                   ->method('hasModules')
                   ->will($this->returnValue(false));
            $Router->expects($this->once())
                   ->method('setModules')
                   ->with($this->equalTo($modules));

            $Application = $this->getMock('Brickoo\Core\Application');
            $Application->expects($this->once())
                        ->method('Router')
                        ->will($this->returnValue($Router));
            $Application->expects($this->once())
                        ->method('getModules')
                        ->will($this->returnValue($modules));

            $Event = $this->getMock('Brickoo\Event\Event', array('Sender'), array('testEvent'));
            $Event->expects($this->once())
                  ->method('Sender')
                  ->will($this->returnValue($Application));

            $this->assertNull($this->Runner->boot($Event));
        }

        /**
         * Test if the listener shutdowns the session by stopping it.
         * @covers Brickoo\Core\Runner::shutdown
         * @covers Brickoo\Core\Runner::stopSession
         */
        public function testShutdown() {
            $SessionManager = $this->getMock('Brickoo\Http\Session\Manager', array('hasSessionStarted'),
                array($this->getMock('Brickoo\Http\Session\Handler\Interfaces\SessionHandler'))
            );
            $SessionManager->expects($this->once())
                           ->method('hasSessionStarted')
                           ->will($this->returnValue(false));

            $Application = $this->getMock('Brickoo\Core\Application');
            $Application->expects($this->once())
                        ->method('SessionManager')
                        ->will($this->returnValue($SessionManager));

            $Event = $this->getMock('Brickoo\Event\Event', array('Sender'), array('testEvent'));
            $Event->expects($this->once())
                  ->method('Sender')
                  ->will($this->returnValue($Application));

            $this->assertNull($this->Runner->shutdown($Event));
        }

        /**
         * Test if the request route is returned.
         * @covers Brickoo\Core\Runner::getRequestRoute
         */
        public function testGetRequestRoute() {
            $response = 'The request route object response.';

            $Router = $this->getMock(
                'Brickoo\Routing\Router', array('getRequestRoute'), array($this->getMock('Brickoo\Http\Request'))
            );
            $Router->expects($this->once())
                   ->method('getRequestRoute')
                   ->will($this->returnValue($response));

            $Application = $this->getMock('Brickoo\Core\Application');
            $Application->expects($this->once())
                        ->method('Router')
                        ->will($this->returnValue($Router));

            $Event = $this->getMock('Brickoo\Event\Event', array('Sender'), array('testEvent'));
            $Event->expects($this->once())
                  ->method('Sender')
                  ->will($this->returnValue($Application));

            $this->assertEquals($response, $this->Runner->getRequestRoute($Event));
        }

        /**
         * Test if the session can be started and the session configuration event is triggered.
         * @covers Brickoo\Core\Runner::startSession
         */
        public function testStartSession() {
            $Route = $this->getMock('Brickoo\Routing\Route', array('isSessionRequired'), array('testRoute'));
            $Route->expects($this->once())
                  ->method('isSessionRequired')
                  ->will($this->returnValue(true));

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', array('getModuleRoute'), array($Route));
            $RequestRoute->expects($this->once())
                         ->method('getModuleRoute')
                         ->will($this->returnValue($Route));

            $SessionManager = $this->getMock('Brickoo\Http\Session\Manager', array('hasSessionStarted', 'start'),
                array($this->getMock('Brickoo\Http\Session\Handler\Interfaces\SessionHandler'))
            );
            $SessionManager->expects($this->once())
                           ->method('hasSessionStarted')
                           ->will($this->returnValue(false));
            $SessionManager->expects($this->once())
                           ->method('start');

            $EventManager = $this->getMock('Brickoo\Event\Manager');
            $EventManager->expects($this->once())
                         ->method('notify')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'));

            $Application = $this->getMock('Brickoo\Core\Application');
            $Application->expects($this->any())
                        ->method('Route')
                        ->will($this->returnValue($RequestRoute));
            $Application->expects($this->any())
                        ->method('SessionManager')
                        ->will($this->returnValue($SessionManager));
            $Application->expects($this->any())
                        ->method('EventManager')
                        ->will($this->returnValue($EventManager));

            $this->assertSame($this->Runner, $this->Runner->startSession($Application));
        }

        /**
         * Test if the session manager is called to stop the session.
         * @covers Brickoo\Core\Runner::stopSession
         */
        public function testStopSession() {
            $SessionManager = $this->getMock('Brickoo\Http\Session\Manager', array('hasSessionStarted', 'stop'),
            array($this->getMock('Brickoo\Http\Session\Handler\Interfaces\SessionHandler'))
            );
            $SessionManager->expects($this->once())
                           ->method('hasSessionStarted')
                           ->will($this->returnValue(true));
            $SessionManager->expects($this->once())
                           ->method('stop');

            $Application = $this->getMock('Brickoo\Core\Application');
            $Application->expects($this->any())
                        ->method('SessionManager')
                        ->will($this->returnValue($SessionManager));

            $this->assertSame($this->Runner, $this->Runner->stopSession($Application));
        }

        /**
         * Test if the response is returned if the response is loaded.
         * @covers Brickoo\Core\Runner::getResponse
         */
        public function testGetResponse() {
            $callback = function($Event) {
                if ($Event->getName() == \Brickoo\Core\Events::EVENT_RESPONSE_LOAD) {
                    return 'The loaded response.';
                }
            };

            $Route = $this->getMock('Brickoo\Routing\Route', array('isSessionRequired', 'isCacheable'), array('testRoute'));
            $Route->expects($this->once())
                  ->method('isSessionRequired')
                  ->will($this->returnValue(false));
            $Route->expects($this->once())
                  ->method('isCacheable')
                  ->will($this->returnValue(true));

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', array('getModuleRoute'), array($Route));
            $RequestRoute->expects($this->any())
                         ->method('getModuleRoute')
                         ->will($this->returnValue($Route));

            $EventManager = $this->getMock('Brickoo\Event\Manager');
            $EventManager->expects($this->once())
                         ->method('ask')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Event'))
                         ->will($this->returnCallback($callback));

            $Application = $this->getMock('Brickoo\Core\Application');
            $Application->expects($this->any())
                        ->method('Route')
                        ->will($this->returnValue($RequestRoute));

            $Event = $this->getMock('Brickoo\Event\Event', array('Sender', 'EventManager'), array('testEvent'));
            $Event->expects($this->any())
                  ->method('Sender')
                  ->will($this->returnValue($Application));
            $Event->expects($this->once())
                  ->method('EventManager')
                  ->will($this->returnValue($EventManager));

            $this->assertEquals('The loaded response.', $this->Runner->getResponse($Event));
        }

        /**
         * Test if trying to save the response without beeing enabled by the route does the event stop.
         * @covers Brickoo\Core\Runner::saveResponse
         */
        public function testSaveResponse() {
            $Route = $this->getMock('Brickoo\Routing\Route', array('isCacheable'), array('testRoute'));
            $Route->expects($this->once())
                  ->method('isCacheable')
                  ->will($this->returnValue(false));

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', array('getModuleRoute'), array($Route));
            $RequestRoute->expects($this->any())
                         ->method('getModuleRoute')
                         ->will($this->returnValue($Route));

            $Application = $this->getMock('Brickoo\Core\Application');
            $Application->expects($this->any())
                        ->method('Route')
                        ->will($this->returnValue($RequestRoute));

            $Event = $this->getMock('Brickoo\Event\Event', array('Sender', 'stop'), array('testEvent'));
            $Event->expects($this->any())
                  ->method('Sender')
                  ->will($this->returnValue($Application));
            $Event->expects($this->once())
                  ->method('stop');

            $this->assertNull($this->Runner->saveResponse($Event));
        }

    }