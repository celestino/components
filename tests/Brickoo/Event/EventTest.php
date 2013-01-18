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

    namespace Tests\Brickoo\Event;

    use Brickoo\Event\Event;

    /**
     * EventTest
     *
     * Test suite for the Event class.
     * @see Brickoo\Event\Event
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Event\Event::__construct
         */
        public function testConstructorInitialization() {
            $Event = new Event('unittest', ($obj = new \stdClass()), array('key' => 'value'));
            $this->assertInstanceOf('Brickoo\Event\Event', $Event);
            $this->assertAttributeEquals('unittest', 'name', $Event);
            $this->assertAttributeSame($obj, 'Sender', $Event);
            $this->assertAttributeEquals(array('key' => 'value'), 'params', $Event);
        }

        /**
         * @covers Brickoo\Event\Event::isStopped
         * @covers Brickoo\Event\Event::stop
         */
        public function testStopRoutine() {
            $Event = new Event('test.event');
            $this->assertAttributeEquals(false, 'stopped', $Event);
            $this->assertFalse($Event->isStopped());
            $this->assertSame($Event, $Event->stop());
            $this->assertAttributeEquals(true, 'stopped', $Event);
            $this->assertTrue($Event->isStopped());
        }

        /**
         * @covers Brickoo\Event\Event::getName
         */
        public function testGetName() {
            $Event = new Event('test.event');
            $this->assertEquals('test.event', $Event->getName());
        }

        /**
         * @covers Brickoo\Event\Event::getParams
         * @covers Brickoo\Event\Event::getParam
         * @covers Brickoo\Event\Event::hasParam
         */
        public function testParamsRoutine() {
            $Event = new Event('test.event', null, array('key' =>'value'));
            $this->assertFalse($Event->hasParam('none'));
            $this->assertEquals(null, $Event->getParam('none'));
            $this->assertTrue($Event->hasParam('key'));
            $this->assertEquals('value', $Event->getParam('key'));
            $this->assertEquals(array('key' => 'value'), $Event->getParams());
        }

        /**
         * @covers Brickoo\Event\Event::getParam
         * @expectedException InvalidArgumentException
         */
        public function testGetParamArgumentException() {
            $Event = new Event('test.event');
            $Event->getParam(array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Event::hasParam
         * @expectedException InvalidArgumentException
         */
        public function testHasParamArgumentException() {
            $Event = new Event('test.event');
            $Event->hasParam(array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Event::hasParams
         */
        public function testHasParams() {
            $Event = new Event('test.event', null, array('id' => 1, 'name' => 'tester'));
            $this->assertFalse($Event->hasParams('unknowed', 'notAvailable'));
            $this->assertTrue($Event->hasParams('id', 'name'));
        }

        /**
         * @covers Brickoo\Event\Event::getSender
         */
        public function testGetSender() {
            $Event = new Event('test.event', ($Sender = new \stdClass()));
            $this->assertAttributeSame($Sender, 'Sender', $Event);
            $this->assertSame($Sender, $Event->getSender());
        }

    }
