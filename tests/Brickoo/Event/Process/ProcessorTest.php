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

    namespace Tests\Brickoo\Event;

    use Brickoo\Event\Process\Processor;

    /**
     * ProcessorTest
     *
     * Test suite for the Processor class.
     * @see Brickoo\Event\Process\Processor
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ProcessorTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Event\Process\Processor
         */
        public function testConstructorImplementedInterfaces() {
            $Processor = new Processor();
            $this->assertInstanceOf('Brickoo\Event\Process\Interfaces\Processor', $Processor);
        }

        /**
         * @covers Brickoo\Event\Process\Processor::handle
         * @covers Brickoo\Event\Process\Processor::hasValidCondition
         */
        public function testHandlingEventWithoutCondition() {
            $eventName = 'test.processor.handle';
            $listenerCallback = function($Event, $EventManager){ return $Event->getName();};

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->once())
                     ->method('getCallback')
                     ->will($this->returnValue($listenerCallback));
            $Listener->expects($this->once())
                     ->method('getCondition')
                     ->will($this->returnValue(null));

            $Processor = new Processor();
            $this->assertEquals($eventName, $Processor->handle($EventManager, $Event, $Listener));
        }

        /**
         * @covers Brickoo\Event\Process\Processor::handle
         * @covers Brickoo\Event\Process\Processor::hasValidCondition
         */
        public function testHandlingWithInvalidCondition() {
            $listenerCondition = function(){ return false; };

            $EventManager = $this->getMock('Brickoo\Event\Interfaces\Manager');
            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->once())
                     ->method('getCondition')
                     ->will($this->returnValue($listenerCondition));

            $Processor = new Processor();
            $this->assertNull($Processor->handle($EventManager, $Event, $Listener));
        }

    }