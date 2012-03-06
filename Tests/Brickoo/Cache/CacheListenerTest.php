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

    use Brickoo\Cache\CacheListener;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * CacheListenerTest
     *
     * Test suite for the CacheListener class.
     * @see Brickoo\Cache\CacheListener
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheListenerTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Test if the constructor intializes the class properties.
         * @covers Brickoo\Cache\CacheListener::__construct
         */
        public function testConstruct()
        {
            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\CacheManagerInterface');
            $CacheListener = new CacheListener($CacheManager, 222);
            $this->assertAttributeSame($CacheManager, 'CacheManager', $CacheListener);
            $this->assertAttributeEquals(222, 'listenerPriority', $CacheListener);
        }

        /**
         * Test if the event listeners can be aggregated.
         * @covers Brickoo\Cache\CacheListener::aggregateListeners
         * @covers Brickoo\Cache\CacheEvents
         */
        public function testAggregateListeners()
        {
            $EventManager = $this->getMock('Brickoo\Event\EventManager');
            $EventManager->expects($this->exactly(5))
                         ->method('attachListener');

            $CacheManager = $this->getMock('Brickoo\Cache\Interfaces\CacheManagerInterface');

            $CacheListener = new CacheListener($CacheManager, 111);
            $this->assertAttributeSame($CacheManager, 'CacheManager', $CacheListener);
            $this->assertAttributeEquals(111, 'listenerPriority', $CacheListener);
            $this->assertNull($CacheListener->aggregateListeners($EventManager));
        }

    }