<?php

    /*
     * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Brickoo\Tests\Event\Builder;

    use Brickoo\Event\Builder\EventManagerBuilder;

    /**
     * EventManagerBuilderTest
     *
     * Test suite for the EventManagerBuilder class.
     * @see Brickoo\Event\Builder\EventManagerBuilder
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventManagerBuilderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::__construct
         */
        public function testConstructor() {
            $Builder = new EventManagerBuilder();
            $this->assertInstanceOf('Brickoo\Event\Builder\Interfaces\EventManagerBuilder', $Builder);
            $this->assertAttributeEquals(array(), "listeners", $Builder);
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::setEventProcessor
         */
        public function testSetEventProcessor() {
            $Processor = $this->getMock('Brickoo\Event\Process\Interfaces\Processor');
            $Builder = new EventManagerBuilder();
            $this->assertSame($Builder, $Builder->setEventProcessor($Processor));
            $this->assertAttributeSame($Processor, "Processor", $Builder);
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::setListenerCollection
         */
        public function testSetListenerCollection() {
            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $Builder = new EventManagerBuilder();
            $this->assertSame($Builder, $Builder->setListenerCollection($ListenerCollection));
            $this->assertAttributeSame($ListenerCollection, "ListenerCollection", $Builder);
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::setEventList
         */
        public function testSetEventList() {
            $EventList = $this->getMock('Brickoo\Memory\Interfaces\Container');
            $Builder = new EventManagerBuilder();
            $this->assertSame($Builder, $Builder->setEventList($EventList));
            $this->assertAttributeSame($EventList, "EventList", $Builder);
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::setListeners
         */
        public function testSetListenersWithArrayContainer() {
            $listeners = array(
                $this->getMock('Brickoo\Event\Interfaces\Listener'),
                $this->getMock('Brickoo\Event\Interfaces\Listener')
            );

            $Builder = new EventManagerBuilder();
            $this->assertSame($Builder, $Builder->setListeners($listeners));
            $this->assertAttributeEquals($listeners, "listeners", $Builder);
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::setListeners
         */
        public function testSetListenersWithTraversableClass() {
            $Traversable = new \Brickoo\Memory\Container(array(
                $this->getMock('Brickoo\Event\Interfaces\Listener'),
                $this->getMock('Brickoo\Event\Interfaces\Listener')
            ));

            $Builder = new EventManagerBuilder();
            $this->assertSame($Builder, $Builder->setListeners($Traversable));
            $this->assertAttributeSame($Traversable, "listeners", $Builder);
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::setListeners
         * @expectedException \InvalidArgumentException
         */
        public function testSetListenersThrowsInvalidArgumentException() {
            $Builder = new EventManagerBuilder();
            $Builder->setListeners("wrongType");
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::setListeners
         * @expectedException \InvalidArgumentException
         */
        public function testSetListenersThrowsTraversableValuesException() {
            $Builder = new EventManagerBuilder();
            $Builder->setListeners(array("wrongType"));
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::build
         * @covers Brickoo\Event\Builder\EventManagerBuilder::getEventProcessor
         * @covers Brickoo\Event\Builder\EventManagerBuilder::getListenerCollection
         * @covers Brickoo\Event\Builder\EventManagerBuilder::getEventList
         * @covers Brickoo\Event\Builder\EventManagerBuilder::attachListeners
         */
        public function testBuildAnEventManagerAutomaticly() {
            $Builder = new EventManagerBuilder();
            $this->assertInstanceOf('Brickoo\Event\Interfaces\Manager', $Builder->build());
        }

        /**
         * @covers Brickoo\Event\Builder\EventManagerBuilder::build
         * @covers Brickoo\Event\Builder\EventManagerBuilder::getEventProcessor
         * @covers Brickoo\Event\Builder\EventManagerBuilder::getListenerCollection
         * @covers Brickoo\Event\Builder\EventManagerBuilder::getEventList
         * @covers Brickoo\Event\Builder\EventManagerBuilder::attachListeners
         */
        public function testBuildeAnEventManagerConfigured() {
            $listenerPriority = 100;

            $Listener = $this->getMock('Brickoo\Event\Interfaces\Listener');
            $Listener->expects($this->any())
                     ->method("getPriority")
                     ->will($this->returnValue($listenerPriority));
            $Traversable = new \Brickoo\Memory\Container(array($Listener));

            $Processor = $this->getMock('Brickoo\Event\Process\Interfaces\Processor');
            $EventList = $this->getMock('Brickoo\Memory\Interfaces\Container');

            $ListenerCollection = $this->getMock('Brickoo\Event\Listener\Interfaces\Collection');
            $ListenerCollection->expects($this->once())
                               ->method("add")
                               ->with($Listener, $listenerPriority);

            $Builder = new EventManagerBuilder();
            $Builder->setEventProcessor($Processor)
                    ->setListenerCollection($ListenerCollection)
                    ->setEventList($EventList)
                    ->setListeners($Traversable);

            $EventManager = $Builder->build();
            $this->assertAttributeSame($Processor, "Processor", $EventManager);
            $this->assertAttributeSame($ListenerCollection, "ListenerCollection", $EventManager);
            $this->assertAttributeSame($EventList, "EventList", $EventManager);
        }

    }