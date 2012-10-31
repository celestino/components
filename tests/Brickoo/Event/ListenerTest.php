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

    use Brickoo\Event\Listener;

    /**
     * ListenerTest
     *
     * Test suite for the Listener class.
     * @see Brickoo\Event\Listener
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ListenerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Event\Listener::__construct
         */
        public function testConstructorInitialization() {
            $eventName = 'test.event';
            $callback = function(){};
            $priority = 100;
            $condition = function(){return true;};

            $Listener = new Listener($eventName, $callback, $priority, $condition);
            $this->assertAttributeEquals($eventName, 'eventName', $Listener);
            $this->assertAttributeEquals($callback, 'callback', $Listener);
            $this->assertAttributeEquals($priority, 'priority', $Listener);
            $this->assertAttributeEquals($condition, 'callableCondition', $Listener);
        }

        /**
         * @covers Brickoo\Event\Listener::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructorCallbackArgumentThrowsException() {
            $Listener = new Listener('test.event', 'wrongType', 0);
        }

        /**
         * @covers Brickoo\Event\Listener::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructorConditionArgumentThrowsException() {
            $Listener = new Listener('test.event', function(){}, 0, 'wrongType');
        }

        /**
         * @covers Brickoo\Event\Listener::getEventName
         */
        public function testGetEventName() {
            $eventName = 'test.event';
            $Listener = new Listener($eventName, function(){}, 0);
            $this->assertEquals($eventName, $Listener->getEventName());
        }

        /**
         * @covers Brickoo\Event\Listener::getCallback
         */
        public function testGetCallback() {
            $callback = function(){};
            $Listener = new Listener('test.event', $callback, 0);
            $this->assertSame($callback, $Listener->getCallback());
        }

        /**
         * @covers Brickoo\Event\Listener::getPriority
         */
        public function testGetPriority() {
            $priority = 100;
            $Listener = new Listener('test.event', function(){}, $priority);
            $this->assertEquals($priority, $Listener->getPriority());
        }

        /**
         * @covers Brickoo\Event\Listener::getCondition
         */
        public function testGetCondition() {
            $condition = function(){return true;};
            $Listener = new Listener('test.event', function(){}, 0, $condition);
            $this->assertSame($condition, $Listener->getCondition());
        }

    }