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

    use Brickoo\Event\EventManager;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * EventTest
     *
     * Test suite for the EventManager class.
     * @see Brickoo\Event\EventManager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventManagerTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the EventManager class.
         * @var \Brickoo\Event\EventManager
         */
        protected $EventManager;

        /**
         * Sets up the EventManager instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->EventManager = new EventManager();
        }

        /**
         * Test if the EventManager implements the EventManagerInterface.
         * @covers Brickoo\Event\EventManager::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Event\Interfaces\EventManagerInterface', $this->EventManager);
        }

        /**
         * Test if a listener can be attached.
         * @covers Brickoo\Event\EventManager::attachListener
         * @covers Brickoo\Event\EventManager::getEventListenerQueue
         * @covers Brickoo\Event\EventManager::getUniformEventName
         */
        public function testAttachListener()
        {
            $callback = function(){};
            $this->assertInternalType('string', ($listenerUID = $this->EventManager->attachListener('test', $callback)));

            $expectedListeners = array(
                $listenerUID => array(
                    'callback'    => $callback,
                    'params'      => null
                )
            );
            $this->assertAttributeEquals($expectedListeners, 'listeners', $this->EventManager);
        }

        /**
         * Test if trying to attach a listener without a callback throws an exception.
         * @covers Brickoo\Event\EventManager::attachListener
         * @expectedException InvalidArgumentException
         */
        public function testAttachListenerCallbackException()
        {
            $this->EventManager->attachListener('test', 'fail');
        }

        /**
         * Test if a listener can be detached.
         * @covers Brickoo\Event\EventManager::detachListener
         * @covers Brickoo\Event\EventManager::isListener
         */
        public function testDetachListener()
        {
            $callback = function(){};
            $listenerUID = $this->EventManager->attachListener('test', $callback);
            $expectedListeners = array(
                $listenerUID => array(
                    'callback'    => $callback,
                    'params'      => null
                )
            );
            $this->assertAttributeEquals($expectedListeners, 'listeners', $this->EventManager);
            $this->assertSame($this->EventManager, $this->EventManager->detachListener($listenerUID));
            $this->assertAttributeEquals(array(), 'listeners', $this->EventManager);
        }

        /**
         * Test if the avaibility of listeners is recognized.
         * @covers Brickoo\Event\EventManager::hasEventListeners
         * @covers Brickoo\Event\EventManager::getUniformEventName
         */
        public function testHasEventListeners()
        {
            $this->assertFalse($this->EventManager->hasEventListeners('test'));
            $this->EventManager->attachListener('test', function() {});
            $this->assertTrue($this->EventManager->hasEventListeners('test'));
        }

        /**
         * @covers Brickoo\Event\EventManager::notify
         * @covers Brickoo\Event\EventManager::getCallbackArguments
         * @covers Brickoo\Event\EventManager::call
         * @covers Brickoo\Event\EventManager::isEventProcessing
         * @covers Brickoo\Event\EventManager::addEventProcessing
         * @covers Brickoo\Event\EventManager::removeProcessedEvent
         */
        public function testNotify()
        {
            $Event = $this->getMock('Brickoo\Event\Event',
                array('getName', 'hasEventManager', 'getParams', 'getParam'),
                array('test.event')
            );
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.event'));
            $Event->expects($this->any())
                  ->method('hasEventManager')
                  ->will($this->returnValue(false));
            $Event->expects($this->any())
                  ->method('getParams')
                  ->will($this->returnValue(array('Class' => 'some class')));
            $Event->expects($this->any())
                  ->method('getParam')
                  ->with('Class')
                  ->will($this->returnValue('some class'));

            $this->EventManager->attachListener('test.event', function($eventParam){}, 0, array('Class'));
            $this->assertNull($this->EventManager->notify($Event));
        }

        /**
         * @covers Brickoo\Event\EventManager::notify
         * @covers Brickoo\Event\Exceptions\InfiniteEventLoopException::__construct
         * @expectedException Brickoo\Event\Exceptions\InfiniteEventLoopException
         */
        public function testNotifyInfinitLoopException()
        {
            $Event = $this->getMock('Brickoo\Event\Event',
                array('getName', 'hasEventManager'),
                array('test.event')
            );
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.event'));
            $Event->expects($this->any())
                  ->method('hasEventManager')
                  ->will($this->returnValue(false));

            $this->EventManager->attachListener('test.event', function($Event){
                $Event->EventManager()->notify($Event);
            });
            $this->assertEquals('test.event', $this->EventManager->notify($Event));
        }

        /**
         * @covers Brickoo\Event\EventManager::ask
         * @covers Brickoo\Event\Exceptions\InfiniteEventLoopException::__construct
         * @expectedException Brickoo\Event\Exceptions\InfiniteEventLoopException
         */
        public function testAskInfinitLoopException()
        {
            $Event = $this->getMock('Brickoo\Event\Event',
                array('getName', 'hasEventManager'),
                array('test.event')
            );
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.event'));
            $Event->expects($this->any())
                  ->method('hasEventManager')
                  ->will($this->returnValue(false));

            $this->EventManager->attachListener('test.event', function($Event){
                $Event->EventManager()->ask($Event);
            });
            $this->assertEquals('test.event', $this->EventManager->ask($Event));
        }

        /**
         * @covers Brickoo\Event\EventManager::ask
         * @covers Brickoo\Event\EventManager::getCallbackArguments
         * @covers Brickoo\Event\EventManager::call
         * @covers Brickoo\Event\EventManager::isEventProcessing
         * @covers Brickoo\Event\EventManager::addEventProcessing
         * @covers Brickoo\Event\EventManager::removeProcessedEvent
         */
        public function testAsk()
        {
            $Event = $this->getMock('Brickoo\Event\Event',
                array('getName', 'hasEventManager'),
                array('test.event')
            );
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue('test.event'));
            $Event->expects($this->any())
                  ->method('hasEventManager')
                  ->will($this->returnValue(false));

            $this->EventManager->attachListener('test.event', function($Event){return $Event->getName();});
            $this->assertEquals('test.event', $this->EventManager->ask($Event));
        }

        /**
         * Test if the listener unique identifier is not knowed returns null.
         * @covers Brickoo\Event\EventManager::call
         */
        public function testCall()
        {
            $this->assertNull($this->EventManager->call('unknowed',  new \Brickoo\Event\Event('nothingToDo')));
        }

    }