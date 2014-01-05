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

namespace Brickoo\Tests\Cache;

use Brickoo\Cache\Events,
    Brickoo\Cache\Event\CacheEvent,
    PHPUnit_Framework_TestCase;

/**
 * CacheEventTest
 *
 * Test suite for the CacheEvent class.
 * @see Brickoo\Cache\Event\CacheEvent
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class CacheEventTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Cache\Event\CacheEvent::getIdentifier */
    public function testGetIdentifierReturnsParameterValue() {
        $identifier = "some_identifier";
        $cacheEvent = new CacheEvent(Events::GET, null, [CacheEvent::PARAM_IDENTIFIER => $identifier]);
        $this->assertEquals($identifier, $cacheEvent->getIdentifier());
    }

    /** @covers Brickoo\Cache\Event\CacheEvent::getContent */
    public function testGetContentReturnsParameterValue() {
        $content = "some cached content";
        $cacheEvent = new CacheEvent(Events::GET, null, [CacheEvent::PARAM_CONTENT => $content]);
        $this->assertEquals($content, $cacheEvent->getContent());
    }

    /** @covers Brickoo\Cache\Event\CacheEvent::getCallback */
    public function testGetCallbackReturnsParameterValue() {
        $callback = function(){};
        $cacheEvent = new CacheEvent(Events::GET, null, [CacheEvent::PARAM_CALLBACK => $callback]);
        $this->assertEquals($callback, $cacheEvent->getCallback());
    }

    /** @covers Brickoo\Cache\Event\CacheEvent::getCallbackArguments */
    public function testGetCallbackArgumentsReturnsParameterValue() {
        $callbackArgs = [];
        $cacheEvent = new CacheEvent(Events::GET, null, [CacheEvent::PARAM_CALLBACK_ARGS => $callbackArgs]);
        $this->assertEquals($callbackArgs, $cacheEvent->getCallbackArguments());
    }

    /** @covers Brickoo\Cache\Event\CacheEvent::getLifetime */
    public function testGetLifetimeReturnsParameterValue() {
        $lifetime = 60;
        $cacheEvent = new CacheEvent(Events::GET, null, [CacheEvent::PARAM_LIFETIME => $lifetime]);
        $this->assertEquals($lifetime, $cacheEvent->getLifetime());
    }

}