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

    use Brickoo\Event\Manager;

    /**
     * EventManagerTest
     *
     * Test suite for the EventManager class.
     * @see Brickoo\Event\Manager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventManagerTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Event\Manager::__construct
         */
        public function testImplementsInterfaceAndPropertiesAreSet() {
            $Processor = $this->getMock('Brickoo\Event\Process\Interfaces\Processor');
            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $EventList = $this->getMock('Brickoo\Memory\Interfaces\Container');

            $EventManager = new Manager($Processor, $ListenerCollection, $EventList);
            $this->assertInstanceOf('Brickoo\Event\Interfaces\Manager', $EventManager);
            $this->assertAttributeSame($Processor,'Processor', $EventManager);
            $this->assertAttributeSame($ListenerCollection, 'ListenerCollection', $EventManager);
            $this->assertAttributeSame($EventList, 'EventList', $EventManager);
        }

        /**
         * @covers Brickoo\Event\Manager::attach
         */
        public function testAttachListener() {
            $listenerUID = uniqid();
            $priority = 100;

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->once())
                     ->method('getPriority')
                     ->will($this->returnValue($priority));

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->once())
                              ->method('add')
                              ->with($Listener, $priority)
                              ->will($this->returnValue($listenerUID));

            $EventManager = new Manager(
                $this->getMock('Brickoo\Event\Process\Interfaces\Processor'),
                $ListenerCollection,
                $this->getMock('Brickoo\Memory\Interfaces\Container')
            );
            $this->assertEquals($listenerUID, $EventManager->attach($Listener));
        }

        /**
         * @covers Brickoo\Event\Manager::attachAggregatedListeners
         */
        public function testAttachAggregatedListeners() {
            require_once "Assets/AggregatedListeners.php";

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->once())
                              ->method('add')
                              ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Listener'), 123)
                              ->will($this->returnValue(uniqid()));

            $EventManager = new Manager(
                $this->getMock('Brickoo\Event\Process\Interfaces\Processor'),
                $ListenerCollection,
                $this->getMock('Brickoo\Memory\Interfaces\Container')
            );

            $Listener = new Assets\AggregatedListeners();
            $this->assertEquals($EventManager, $EventManager->attachAggregatedListeners($Listener));
        }

        /**
         * @covers Brickoo\Event\Manager::detach
         */
        public function testDetachListener() {
            $listenerUID = uniqid();

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->once())
                              ->method('remove')
                              ->with($listenerUID)
                              ->will($this->returnValue(true));

            $EventManager = new Manager(
                $this->getMock('Brickoo\Event\Process\Interfaces\Processor'),
                $ListenerCollection,
                $this->getMock('Brickoo\Memory\Interfaces\Container')
            );
            $this->assertSame($EventManager, $EventManager->detach($listenerUID));
        }

        /**
         * @covers Brickoo\Event\Manager::detach
         * @expectedException InvalidArgumentException
         */
        public function testDetachListenerIdentifierThrowsArgumentException() {
            $EventManager = new Manager(
                $this->getMock('Brickoo\Event\Process\Interfaces\Processor'),
                $this->getMock('Brickoo\Event\Listener\Interfaces\Collection'),
                $this->getMock('Brickoo\Memory\Interfaces\Container')
            );
            $EventManager->detach(array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Manager::notify
         * @covers Brickoo\Event\Manager::process
         * @covers Brickoo\Event\Manager::getEventListenersResponse
         */
        public function testNotify() {
            $eventName = 'test.event.manager.notify';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $EventManager = $this->getEventManagerFixture($eventName, $Event);
            $this->assertSame($EventManager, $EventManager->notify($Event));
        }

        /**
         * @covers Brickoo\Event\Manager::notifyOnce
         * @covers Brickoo\Event\Manager::process
         * @covers Brickoo\Event\Manager::getEventListenersResponse
         */
        public function testNotifyOnce() {
            $eventName = 'test.event.manager.notifyOnce';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $EventManager = $this->getEventManagerFixture($eventName, $Event);
            $this->assertSame($EventManager, $EventManager->notifyOnce($Event));
        }

        /**
         * @covers Brickoo\Event\Manager::ask
         * @covers Brickoo\Event\Manager::process
         * @covers Brickoo\Event\Manager::getEventListenersResponse
         */
        public function testAskWithResponse() {
            $expectedResult = 'result of asking for response';
            $eventName = 'test.event.manager.ask';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $EventManager = $this->getEventManagerFixture($eventName, $Event, $expectedResult);
            $this->assertInstanceOf(
                'Brickoo\Event\Response\Interfaces\Collection',
                ($ResponseCollection = $EventManager->ask($Event))
            );
            $this->assertEquals($expectedResult, $ResponseCollection->shift());
        }

        /**
         * @covers Brickoo\Event\Manager::ask
         * @covers Brickoo\Event\Manager::process
         * @covers Brickoo\Event\Manager::getEventListenersResponse
         */
        public function testAskWithEmptyResponse() {
            $eventName = 'test.event.manager.ask';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $EventManager = $this->getEventManagerFixture($eventName, $Event);
            $this->assertInstanceOf(
                'Brickoo\Event\Response\Interfaces\Collection',
                ($ResponseCollection = $EventManager->ask($Event))
            );
            $this->assertTrue($ResponseCollection->isEmpty());
        }

    /**
         * @covers Brickoo\Event\Manager::collect
         * @covers Brickoo\Event\Manager::process
         * @covers Brickoo\Event\Manager::getEventListenersResponse
         */
        public function testCollectWithResponse() {
            $expectedResult = 'result of asking for response';
            $eventName = 'test.event.manager.collect';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $EventManager = $this->getEventManagerFixture($eventName, $Event, $expectedResult);
            $this->assertInstanceOf(
                'Brickoo\Event\Response\Interfaces\Collection',
                ($ResponseCollection = $EventManager->collect($Event))
            );
            $this->assertEquals($expectedResult, $ResponseCollection->shift());
        }

        /**
         * @covers Brickoo\Event\Manager::collect
         * @covers Brickoo\Event\Manager::process
         * @covers Brickoo\Event\Manager::getEventListenersResponse
         */
        public function testCollectWithEmptyResponse() {
            $eventName = 'test.event.manager.collect';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $EventManager = $this->getEventManagerFixture($eventName, $Event);
            $this->assertInstanceOf(
                'Brickoo\Event\Response\Interfaces\Collection',
                ($ResponseCollection = $EventManager->collect($Event))
            );
            $this->assertTrue($ResponseCollection->isEmpty());
        }

        /**
        * @covers Brickoo\Event\Manager::process
        * @covers Brickoo\Event\Exceptions\InfiniteEventLoop
        * @expectedException Brickoo\Event\Exceptions\InfiniteEventLoop
        */
        public function testProcessInfiniteLoopIsDetected() {
            $eventName = 'test.event.manager.infinite.loop';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $EventList = $this->getMock('Brickoo\Memory\Interfaces\Container');
            $EventList->expects($this->once())
                      ->method('has')
                      ->with($eventName)
                      ->will($this->returnValue(true));

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->any())
                               ->method('hasListeners')
                               ->will($this->returnValue(true));

            $EventManager = new Manager(
                $this->getMock('Brickoo\Event\Process\Interfaces\Processor'),
                $ListenerCollection,
                $EventList
            );
            $EventManager->notify($Event);
        }

        /**
         * @covers Brickoo\Event\Manager::process
         */
        public function testNotificationWithoutListeners() {
            $eventName = 'test.event.manager.notify';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->any())
                               ->method('hasListeners')
                               ->will($this->returnValue(false));

            $EventManager = new Manager(
                $this->getMock('Brickoo\Event\Process\Interfaces\Processor'),
                $ListenerCollection,
                $this->getMock('Brickoo\Memory\Interfaces\Container')
            );
            $this->assertSame($EventManager, $EventManager->notify($Event));
        }

        /**
         * @covers Brickoo\Event\Manager::process
         */
        public function testResponseCollectionIsReturnedWithoutHavingListeners() {
            $eventName = 'test.event.manager.collect';

            $Event = $this->getMock('Brickoo\Event\Interfaces\Event');
            $Event->expects($this->any())
                  ->method('getName')
                  ->will($this->returnValue($eventName));

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->any())
                               ->method('hasListeners')
                               ->will($this->returnValue(false));

            $EventManager = new Manager(
                $this->getMock('Brickoo\Event\Process\Interfaces\Processor'),
                $ListenerCollection,
                $this->getMock('Brickoo\Memory\Interfaces\Container')
            );
            $this->assertInstanceOf('\Brickoo\Event\Response\Interfaces\Collection', $EventManager->collect($Event));
        }

        /**
         * Returns an event manager fixture configured with the arguments.
         * @param string $eventName the event name
         * @param \Brickoo\Event\Interfaces\Event $Event the event triggered
         * @param string|null $expectedResult the expected processor result
         * @return \Brickoo\Event\Manager
         */
        private function getEventManagerFixture($eventName, \Brickoo\Event\Interfaces\Event $Event, $expectedResult = null) {
            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');

            $Processor = $this->getMock('Brickoo\Event\Process\Interfaces\Processor');

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->any())
                               ->method('hasListeners')
                               ->will($this->returnValue(true));
            $ListenerCollection->expects($this->any())
                               ->method('getListeners')
                               ->will($this->returnValue(array($Listener)));

            $EventList = $this->getMock('Brickoo\Memory\Interfaces\Container');
            $EventList->expects($this->once())
                      ->method('has')
                      ->with($eventName)
                      ->will($this->returnValue(false));
            $EventList->expects($this->once())
                      ->method('set')
                      ->with($eventName, $this->isType('integer'))
                      ->will($this->returnSelf());
            $EventList->expects($this->once())
                      ->method('delete')
                      ->with($eventName)
                      ->will($this->returnSelf());

            $EventManager = new Manager($Processor, $ListenerCollection, $EventList);

            $Processor->expects($this->once())
                      ->method('handle')
                      ->with($EventManager, $Event, $Listener)
                      ->will($this->returnValue($expectedResult));

            return $EventManager;
        }

    }