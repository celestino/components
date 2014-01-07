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

namespace Brickoo\Tests\Cache;

use Brickoo\Cache\Events,
    Brickoo\Cache\Event\RetrieveByCallbackEvent,
    PHPUnit_Framework_TestCase;

/**
 * RetrieveByCallbackEventTest
 *
 * Test suite for the RetrieveByCallbackEvent class.
 * @see Brickoo\Cache\Event\RetrieveByCallbackEvent
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RetrieveByCallbackEventTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Cache\Event\RetrieveByCallbackEvent::__construct */
    public function testConstructorInitializesProperties() {
        $identifier = "some_identifier";
        $callback = function(){};
        $callbackArguments = [];
        $cacheLifetime = 60;

        $event = new RetrieveByCallbackEvent($identifier, $callback, $callbackArguments, $cacheLifetime);
        $this->assertInstanceOf("\\Brickoo\\Cache\\Event\\CacheEvent", $event);
        $this->assertAttributeEquals(Events::CALLBACK, "name", $event);
        $this->assertAttributeEquals(
            [RetrieveByCallbackEvent::PARAM_IDENTIFIER => $identifier,
            RetrieveByCallbackEvent::PARAM_CALLBACK => $callback,
            RetrieveByCallbackEvent::PARAM_CALLBACK_ARGS => $callbackArguments,
            RetrieveByCallbackEvent::PARAM_LIFETIME => $cacheLifetime],
            "params", $event
        );
    }

    /**
     * @covers Brickoo\Cache\Event\RetrieveByCallbackEvent::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsExceptionForInvalidIdentifierArgument() {
        $event = new RetrieveByCallbackEvent(["wrongType"], function(){});
    }

    /**
     * @covers Brickoo\Cache\Event\RetrieveByCallbackEvent::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsExceptionForInvalidLifetimeArgument() {
        $event = new RetrieveByCallbackEvent(
            "identifier", function(){}, [], "invalidType"
        );
    }

}