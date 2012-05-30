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

    use Brickoo\Event\Manager;

    require_once ('PHPUnit/Autoload.php');

    /**
     * EventTest
     *
     * Test suite for the EventManager class.
     * @see Brickoo\Event\Manager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventManagerTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the EventManager class.
         * @var \Brickoo\Event\Manager
         */
        protected $EventManager;

        /**
         * Sets up the EventManager instance used.
         * @return void
         */
        protected function setUp() {
            $this->EventManager = new Manager();
        }

        /**
         * Test if the EventManager implements the EventManager.
         * @covers Brickoo\Event\Manager::__construct
         */
        public function testConstruct() {
            $this->assertInstanceOf('Brickoo\Event\Interfaces\Manager', $this->EventManager);
        }

        /**
         * Test if the static Mananager instance canbe injected.
         * @covers Brickoo\Event\Manager::Instance
         */
        public function testStaticInstanceInjection() {
            require_once 'Fixture/EventManagerFixture.php';
            $EventManager = new Fixture\EventManagerFixture();
            $EventManager->resetInstance();

            $ExpectedManager = new Manager();

            Manager::Instance($ExpectedManager);
            $this->assertSame($ExpectedManager, Manager::Instance());

            $EventManager->resetInstance();
        }

        /**
         * Test if the static Manager can be lazy initialized.
         * @covers Brickoo\Event\Manager::Instance
         */
        public function testStaticInstanceLazyInitialization() {
            require_once 'Fixture/EventManagerFixture.php';
            $EventManager = new Fixture\EventManagerFixture();
            $EventManager->resetInstance();

            $this->assertInstanceOf('Brickoo\Event\Interfaces\Manager', Manager::Instance());

            $EventManager->resetInstance();
        }

        /**
         * Test if aggregated listeners would be attached.
         * @covers Brickoo\Event\Manager::attachAggregatedListeners
         */
        public function testAttachAggregatedListeners() {
            $Listener = $this->getMock('\Brickoo\Event\Interfaces\ListenerAggregate', array('aggregateListeners'));
            $Listener->expects($this->once())
                     ->method('aggregateListeners')
                     ->with($this->EventManager)
                     ->will($this->returnValue(null));

            $this->assertSame($this->EventManager, $this->EventManager->attachAggregatedListeners($Listener));
        }

        /**
         * Test if a listener can be attached.
         * @covers Brickoo\Event\Manager::attachListener
         * @covers Brickoo\Event\Manager::getEventListenerQueue
         * @covers Brickoo\Event\Manager::getUniformEventName
         */
        public function testAttachListener() {
            $callback = function(){};
            $this->assertInternalType('string', ($listenerUID = $this->EventManager->attachListener('test', $callback)));

            $expectedListeners = array(
                $listenerUID => array(
                    'callback'    => $callback,
                    'params'      => null,
                    'condition'   => null
                )
            );
            $this->assertAttributeEquals($expectedListeners, 'listeners', $this->EventManager);
        }

        /**
         * Test if trying to attach a listener without a valid callback throws an exception.
         * @covers Brickoo\Event\Manager::attachListener
         * @expectedException InvalidArgumentException
         */
        public function testAttachListenerCallbackException() {
            $this->EventManager->attachListener('test', 'wrongType');
        }

        /**
         * Test if trying to attach a listener without a valid condition throws an exception.
         * @covers Brickoo\Event\Manager::attachListener
         * @expectedException InvalidArgumentException
         */
        public function testAttachListenerConditionException() {
            $this->EventManager->attachListener('test', function(){}, 0, null, 'wrongType');
        }

        /**
         * Test if a listener can be detached.
         * @covers Brickoo\Event\Manager::detachListener
         * @covers Brickoo\Event\Manager::isListener
         */
        public function testDetachListener() {
            $callback = function(){};
            $listenerUID = $this->EventManager->attachListener('test', $callback);
            $expectedListeners = array(
                $listenerUID => array(
                    'callback'    => $callback,
                    'params'      => null,
                    'condition'   => null
                )
            );
            $this->assertAttributeEquals($expectedListeners, 'listeners', $this->EventManager);
            $this->assertSame($this->EventManager, $this->EventManager->detachListener($listenerUID));
            $this->assertAttributeEquals(array(), 'listeners', $this->EventManager);
        }

        /**
         * Test if the avaibility of listeners is recognized.
         * @covers Brickoo\Event\Manager::hasEventListeners
         * @covers Brickoo\Event\Manager::getUniformEventName
         */
        public function testHasEventListeners() {
            $this->assertFalse($this->EventManager->hasEventListeners('test'));
            $this->EventManager->attachListener('test', function() {});
            $this->assertTrue($this->EventManager->hasEventListeners('test'));
        }

        /**
         * @covers Brickoo\Event\Manager::notify
         * @covers Brickoo\Event\Manager::getCallbackArguments
         * @covers Brickoo\Event\Manager::call
         * @covers Brickoo\Event\Manager::isEventProcessing
         * @covers Brickoo\Event\Manager::addEventProcessing
         * @covers Brickoo\Event\Manager::removeProcessedEvent
         * @covers Brickoo\Event\Manager::processEvent
         */
        public function testNotify() {
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
         * @covers Brickoo\Event\Manager::notifyOnce
         * @covers Brickoo\Event\Manager::getCallbackArguments
         * @covers Brickoo\Event\Manager::call
         * @covers Brickoo\Event\Manager::isEventProcessing
         * @covers Brickoo\Event\Manager::addEventProcessing
         * @covers Brickoo\Event\Manager::removeProcessedEvent
         * @covers Brickoo\Event\Manager::processEvent
         */
        public function testNotifyOnce() {
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
            $this->assertNull($this->EventManager->notifyOnce($Event));
        }

        /**
         * @covers Brickoo\Event\Manager::notify
         * @covers Brickoo\Event\Manager::processEvent
         * @covers Brickoo\Event\Exceptions\InfiniteEventLoopException::__construct
         * @expectedException Brickoo\Event\Exceptions\InfiniteEventLoopException
         */
        public function testNotifyInfinitLoopException() {
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
         * @covers Brickoo\Event\Manager::ask
         * @covers Brickoo\Event\Manager::processEvent
         * @covers Brickoo\Event\Exceptions\InfiniteEventLoopException::__construct
         * @expectedException Brickoo\Event\Exceptions\InfiniteEventLoopException
         */
        public function testAskInfinitLoopException() {
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
         * @covers Brickoo\Event\Manager::ask
         * @covers Brickoo\Event\Manager::processEvent
         * @covers Brickoo\Event\Manager::getCallbackArguments
         * @covers Brickoo\Event\Manager::call
         * @covers Brickoo\Event\Manager::isEventProcessing
         * @covers Brickoo\Event\Manager::addEventProcessing
         * @covers Brickoo\Event\Manager::removeProcessedEvent
         */
        public function testAsk() {
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
         * Test if the iteration over the listener works and the first response is returned.
         * @covers Brickoo\Event\Manager::ask
         */
        public function testAskReturnsTheResponse() {
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

            $this->EventManager->attachListener('test.event', function($Event){return null;}, 100);
            $this->EventManager->attachListener('test.event', function($Event){return '2nd Listener';}, 50);
            $this->EventManager->attachListener('test.event', function($Event){return '3nd Listener';}, 0);
            $this->assertEquals('2nd Listener', $this->EventManager->ask($Event));
        }

        /**
         * Test if the listener is not knowed returns null.
         * @covers Brickoo\Event\Manager::call
         */
        public function testCall() {
            $this->assertNull($this->EventManager->call('unknowed',  new \Brickoo\Event\Event('nothingToDo')));
        }

        /**
         * Test if the condition fails the call returns null.
         * @covers Brickoo\Event\Manager::call
         * @covers Brickoo\Event\Manager::getCallbackArguments
         */
        public function testCallbackConditionFails() {
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

            $listenerUID = $this->EventManager->attachListener('test.event', function(){}, 0, null,
                function($Event){return ($Event->getName() == 'fail');}
            );
            $this->assertNull($this->EventManager->call($listenerUID, $Event));
        }

        /**
         * Test if the condition fails the call returns null.
         * @covers Brickoo\Event\Manager::call
         * @covers Brickoo\Event\Manager::getCallbackArguments
         */
        public function testExpectedArgumentsFails() {
            $Event = $this->getMock('Brickoo\Event\Event',
                array('getName', 'hasEventManager', 'getParams'),
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
                  ->will($this->returnValue(array()));

            $listenerUID = $this->EventManager->attachListener('test.event', function(){}, 0, array('Param'));
            $this->assertNull($this->EventManager->call($listenerUID, $Event));
        }

    }