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

    namespace Tests\Brickoo\Log;

    use Brickoo\Log\Listener;

    /**
     * ListenerTest
     *
     * Test suite for the Listener class.
     * @see Brickoo\Log\Listener
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LogListenerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Log\Listener::__construct
         */
        public function testConstructor() {
            $Logger = $this->getMock('Brickoo\Log\Interfaces\Logger');
            $Listener = new Listener($Logger, 5);
            $this->assertAttributeSame($Logger, 'Logger', $Listener);
            $this->assertAttributeEquals(5, 'listenerPriority', $Listener);
        }

        /**
         * @covers Brickoo\Log\Listener::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructorThrowsArgumentException() {
            $Logger = $this->getMock('Brickoo\Log\Interfaces\Logger');
            $Listener = new Listener($Logger, 'wrongType');
        }

        /**
         * @covers Brickoo\Log\Listener::attachListeners
         * @covers Brickoo\Log\Events
         */
        public function testAttachListeners() {
            $priority = 10;
            $Logger = $this->getMock('Brickoo\Log\Interfaces\Logger');

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $EventManager->expects($this->once())
                         ->method('attach')
                         ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Listener'))
                         ->will($this->returnSelf());

            $Listener = new Listener($Logger, $priority);
            $this->assertAttributeSame($Logger, 'Logger', $Listener);
            $this->assertAttributeEquals($priority, 'listenerPriority', $Listener);
            $this->assertNull( $Listener->attachListeners($EventManager));
        }

        /**
         * @covers Brickoo\Log\Listener::handleLogEvent
         */
        public function testHandleLogEvent() {
            $priority = 10;

            $messages = array("log this test message");
            $severity = \Brickoo\Log\Logger::SEVERITY_INFO;

            $Logger = $this->getMock('Brickoo\Log\Interfaces\Logger');
            $Logger->expects($this->once())
                   ->method("log")
                   ->with($messages, $severity);

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = $this->getMock('Brickoo\Log\Event\LogEvent', array("getMessages", "getSeverity"), array($messages, $severity));
            $Event->expects($this->once())
                  ->method('getMessages')
                  ->will($this->returnValue($messages));
            $Event->expects($this->once())
                  ->method('getSeverity')
                  ->will($this->returnValue($severity));


            $Listener = new Listener($Logger, 100);
            $Listener->handleLogEvent($Event, $EventManager);
        }

    }