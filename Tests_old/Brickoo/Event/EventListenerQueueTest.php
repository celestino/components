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

    use Brickoo\Event\ListenerQueue;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * EventTest
     *
     * Test suite for the EventListenerQueue class.
     * @see Brickoo\Event\ListenerQueue
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventListenerQueueTest extends \PHPUnit_Framework_TestCase {
        /**
         * Holds an instance of the EventListenerQueue class.
         * @var \Brickoo\Event\ListenerQueue
         */
        protected $EventListenerQueue;

        /**
         * Sets up the the used EventListenerQueue instance.
         * @return void
         */
        protected function setUp() {
            $this->EventListenerQueue = new ListenerQueue();
        }

        /**
         * Test if the serial property is initialized.
         * @covers Brickoo\Event\ListenerQueue::__construct
         */
        public function testConstruct() {
            $this->assertAttributeEquals(PHP_INT_MAX, 'serial', $this->EventListenerQueue);
        }

        /**
         * Test if a value can be inserted to the queue and the priority is respected.
         * @covers Brickoo\Event\ListenerQueue::insert
         */
        public function testInsert() {
            $this->EventListenerQueue->insert('A', 100);
            $this->EventListenerQueue->insert('B', 100);
            $this->EventListenerQueue->insert('C', 200);

            $values = array();
            $queue = clone $this->EventListenerQueue;
            foreach($queue as $value) {
                $values[] = $value;
            }
            $this->assertEquals(array('C', 'A', 'B'), $values);
        }

    }