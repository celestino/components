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

    namespace Tests\Brickoo\Event\Listener;

    use Brickoo\Event\Listener\Collection;

    /**
     * CollectionTest
     *
     * Test suite for the Listener\Collection class.
     * @see Brickoo\Event\Listener\Collection
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CollectionTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Event\Listener\Collection::__construct
         */
        public function testConstructor() {
            $Collection = new Collection();
            $this->assertAttributeEquals(array(), 'listenerQueues', $Collection);
            $this->assertAttributeEquals(array(), 'listeners', $Collection);
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::add
         */
        public function testAddingEventListeners() {
            $eventName = 'test.add.listener';
            $priority = 100;

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->once())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));

            $Collection = new Collection();
            $this->assertEquals(spl_object_hash($Listener), $Collection->add($Listener, $priority));
            $this->assertAttributeContains($Listener, 'listeners', $Collection);
            $this->assertAttributeCount(1, 'listeners', $Collection);
            $this->assertAttributeContainsOnly(
                'Brickoo\Event\Listener\Interfaces\Queue', 'listenerQueues', $Collection
            );
            $this->assertAttributeCount(1, 'listenerQueues', $Collection);
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::add
         * @expectedException \InvalidArgumentException
         */
        public function testAddPriorityArgumentException() {
            $Collection = new Collection();
            $Collection->add($this->getMock('Brickoo\Event\Interfaces\Listener'), array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::get
         */
        public function testGettingAnEventListener() {
            $eventName = 'test.add.listener';
            $priority = 100;

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->once())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));

            $Collection = new Collection();
            $listenerUID = $Collection->add($Listener, $priority);

            $this->assertSame($Listener, $Collection->get($listenerUID));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::get
         * @expectedException \InvalidArgumentException
         */
        public function testGetIdentifierThrowsArgumentException() {
            $Collection = new Collection();
            $Collection->get(array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::get
         * @covers Brickoo\Event\Listener\Exceptions\ListenerNotAvailable
         * @expectedException \Brickoo\Event\Listener\Exceptions\ListenerNotAvailable
         */
        public function testGetHasNotListenerThrowsException() {
            $Collection = new Collection();
            $Collection->get('doesNotExistIdentifier');
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::has
         */
        public function testHasEventListener() {
            $eventName = 'test.add.listener';
            $priority = 100;

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->once())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));

            $Collection = new Collection();
            $listenerUID = $Collection->add($Listener, $priority);

            $this->assertFalse($Collection->has('fail'));
            $this->assertTrue($Collection->has($listenerUID));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::has
         * @expectedException \InvalidArgumentException
         */
        public function testHasIdentifierThrowsArgumentException() {
            $Collection = new Collection();
            $Collection->has(array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::remove
         * @covers Brickoo\Event\Listener\Collection::removeListenerFromQueue
         */
        public function testRemovingEventListener() {
            $eventName = 'test.add.listener';
            $priority = 100;

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->any())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));
            $Listener2 = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener2->expects($this->any())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));

            $Collection = new Collection();
            $listenerUID = $Collection->add($Listener, $priority);
            $Collection->add($Listener2, $priority);

            $this->assertSame($Collection, $Collection->remove($listenerUID));
            $this->assertAttributeContains($Listener2, 'listeners', $Collection);
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::remove
         * @expectedException \InvalidArgumentException
         */
        public function testRemoveIdentifierThrowsArgumentException() {
            $Collection = new Collection();
            $Collection->remove(array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::remove
         * @covers Brickoo\Event\Listener\Exceptions\ListenerNotAvailable
         * @expectedException \Brickoo\Event\Listener\Exceptions\ListenerNotAvailable
         */
        public function testRemoveListenerNotAvailableThrowsException() {
            $Collection = new Collection();
            $Collection->remove('failIdentifier');
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::getListeners
         * @covers Brickoo\Event\Listener\Collection::collectEventListeners
         */
        public function testGetListenersOrderedByPriority() {
            $eventName = 'test.add.listener';

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->any())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));
            $Listener2 = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener2->expects($this->any())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));

            $Collection = new Collection();
            $Collection->add($Listener, 100);
            $Collection->add($Listener2, 500);

            $this->assertEquals(array($Listener2, $Listener),$Collection->getListeners($eventName));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::getListeners
         * @expectedException \InvalidArgumentException
         */
        public function testGetListenersEventNameThrowsArgumentException() {
            $Collection = new  Collection();
            $Collection->getListeners(array('wrongType'));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::getListeners
         * @covers Brickoo\Event\Listener\Exceptions\ListenersNotAvailable
         * @expectedException \Brickoo\Event\Listener\Exceptions\ListenersNotAvailable
         */
        public function testGetListenersNotAvailableThrowsException() {
            $Collection = new Collection();
            $Collection->getListeners('test.get.listeners');
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::hasListeners
         */
        public function testHasListeners() {
            $eventName = 'test.add.listener';
            $priority = 100;

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->any())
                     ->method('getEventName')
                     ->will($this->returnValue($eventName));

            $Collection = new Collection();

            $this->assertFalse($Collection->hasListeners($eventName));
            $listenerUID = $Collection->add($Listener, $priority);
            $this->assertTrue($Collection->hasListeners($eventName));
        }

        /**
         * @covers Brickoo\Event\Listener\Collection::hasListeners
         * @expectedException \InvalidArgumentException
         */
        public function testHasListenerThrowsEventNameArgumentException() {
            $Collection = new Collection();
            $Collection->hasListeners(array('wrongType'));
        }

    }