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

    use Brickoo\Log\Listener;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * ListenerTest
     *
     * Test suite for the Listener class.
     * @see Brickoo\Log\Listener
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LogListenerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Test if the class properties are set.
         * @covers Brickoo\Log\Listener::__construct
         */
        public function testConstruct()
        {
            $Logger = $this->getMock('Brickoo\Log\Interfaces\LoggerInterface');
            $Listener = new Listener($Logger, 5);
            $this->assertAttributeSame($Logger, 'Logger', $Listener);
            $this->assertAttributeEquals(5, 'listenerPriority', $Listener);
        }

        /**
         * Test if trying to set an wrong argumetn type throws an exception.
         * @covers Brickoo\Log\Listener::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructArgumentException()
        {
            $Logger = $this->getMock('Brickoo\Log\Interfaces\LoggerInterface');
            $Listener = new Listener($Logger, 'wrongType');
        }

        /**
         * Test if the aggregated event listeners are attached.
         * @covers Brickoo\Log\Listener::aggregateListeners
         * @covers Brickoo\Log\Events
         */
        public function testAggregateListeners()
        {
            $priority = 10;
            $Logger = $this->getMock('Brickoo\Log\Interfaces\LoggerInterface');

            $EventManager = $this->getMock('Brickoo\Event\Manager', array('attachListener'));
            $EventManager->expects($this->once())
                         ->method('attachListener')
                         ->with(\Brickoo\Log\Events::EVENT_LOG, array($Logger, 'logEvent'), $priority)
                         ->will($this->returnSelf());

            $Listener = new Listener($Logger, $priority);
            $this->assertAttributeSame($Logger, 'Logger', $Listener);
            $this->assertAttributeEquals($priority, 'listenerPriority', $Listener);
            $this->assertSame($Listener, $Listener->aggregateListeners($EventManager));
        }

    }