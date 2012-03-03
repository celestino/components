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

    use Brickoo\Event\Event;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * EventTest
     *
     * Test suite for the Event class.
     * @see Brickoo\Event\Event
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Event class.
         * @var \Brickoo\Event\Event
         */
        protected $Event;

        /**
         * Sets up the used Event instance.
         * @return void
         */
        protected function setUp()
        {
            $this->Event = new Event('test', new stdClass(), array('key' => 'value'));
        }

        /**
         * Test if the Event calss implements the EventInterface.
         * Test if the constructor arguments are properly assigned.
         * @covers Brickoo\Event\Event::__construct
         */
        public function testConstruct()
        {
            $Event = new Event('unittest', ($obj = new stdClass()), array('key' => 'value'));
            $this->assertInstanceOf('Brickoo\Event\Event', $Event);
            $this->assertAttributeEquals('unittest', 'name', $Event);
            $this->assertAttributeSame($obj, 'Sender', $Event);
            $this->assertAttributeEquals(array('key' => 'value'), 'params', $Event);
        }

        /**
         * Test if the EventManager is lazy initialized and assigned to the dependencies.
         * @covers Brickoo\Event\Event::EventManager
         * @covers Brickoo\Event\Event::getDependency
         */
        public function testEventManagerLazyInitialization()
        {
            $this->assertInstanceOf('Brickoo\Event\Interfaces\EventManagerInterface', ($EM = $this->Event->EventManager()));
            $this->assertAttributeContains($EM, 'dependencies', $this->Event);
        }

        /**
         * Test if the EventManager can be injected and is assigned to the dependencies.
         * @covers Brickoo\Event\Event::EventManager
         * @covers Brickoo\Event\Event::getDependency
         */
        public function testEventManagerInjection()
        {
            $EventManager = $this->getMock('Brickoo\Event\EventManager');
            $this->assertSame($this->Event, $this->Event->EventManager($EventManager));
            $this->assertAttributeContains($EventManager, 'dependencies', $this->Event);
        }

        /**
         * Test if the EventManager avaibility is recognized.
         * @covers Brickoo\Event\Event::hasEventManager
         */
        public function testHasEventManager()
        {
            $this->assertFalse($this->Event->hasEventManager());
            $this->Event->EventManager();
            $this->assertTrue($this->Event->hasEventManager());
        }

        /**
         * Test if the vent name can be retrieved.
         * @covers Brickoo\Event\Event::getName
         */
        public function testGetName()
        {
            $this->assertEquals('test', $this->Event->getName());
        }

        /**
         * Test the params routines.
         * @covers Brickoo\Event\Event::getParams
         * @covers Brickoo\Event\Event::getParam
         * @covers Brickoo\Event\Event::hasParam
         */
        public function testParamsRoutine()
        {
            $this->assertFalse($this->Event->hasParam('none'));
            $this->assertTrue($this->Event->hasParam('key'));
            $this->assertEquals(null, $this->Event->getParam('none'));
            $this->assertEquals('value', $this->Event->getParam('key'));
            $this->assertEquals(array('key' => 'value'), $this->Event->getParams());
        }

        /**
         * Test if trying to use a wrong argument type thrwos an exception.
         * @covers Brickoo\Event\Event::getParam
         * @expectedException InvalidArgumentException
         */
        public function testGetParamArgumentException()
        {
            $this->Event->getParam(array('wrongType'));
        }

        /**
         * Test if trying to use a wrong argument type thrwos an exception.
         * @covers Brickoo\Event\Event::hasParam
         * @expectedException InvalidArgumentException
         */
        public function testHasParamArgumentException()
        {
            $this->Event->hasParam(array('wrongType'));
        }

        /**
         * Test if the Sender can be retrieved.
         * @covers Brickoo\Event\Event::Sender
         */
        public function testSender()
        {
            $this->assertInstanceOf('stdClass', $this->Event->Sender());
        }

    }
