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

namespace Brickoo\Tests\Component\Cache\Message;

use Brickoo\Component\Cache\Messages,
    Brickoo\Component\Cache\Message\CacheMessage,
    PHPUnit_Framework_TestCase;

/**
 * CacheMessageTest
 *
 * Test suite for the CacheMessage class.
 * @see Brickoo\Component\Cache\Message\CacheMessage
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class CacheMessageTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Cache\Message\CacheMessage::getIdentifier */
    public function testGetIdentifierReturnsParameterValue() {
        $identifier = "some_identifier";
        $cacheMessage = new CacheMessage(Messages::GET, null, [CacheMessage::PARAM_IDENTIFIER => $identifier]);
        $this->assertEquals($identifier, $cacheMessage->getIdentifier());
    }

    /** @covers Brickoo\Component\Cache\Message\CacheMessage::getContent */
    public function testGetContentReturnsParameterValue() {
        $content = "some cached content";
        $cacheMessage = new CacheMessage(Messages::GET, null, [CacheMessage::PARAM_CONTENT => $content]);
        $this->assertEquals($content, $cacheMessage->getContent());
    }

    /** @covers Brickoo\Component\Cache\Message\CacheMessage::getCallback */
    public function testGetCallbackReturnsParameterValue() {
        $callback = function(){};
        $cacheMessage = new CacheMessage(Messages::GET, null, [CacheMessage::PARAM_CALLBACK => $callback]);
        $this->assertEquals($callback, $cacheMessage->getCallback());
    }

    /** @covers Brickoo\Component\Cache\Message\CacheMessage::getCallbackArguments */
    public function testGetCallbackArgumentsReturnsParameterValue() {
        $callbackArgs = [];
        $cacheMessage = new CacheMessage(Messages::GET, null, [CacheMessage::PARAM_CALLBACK_ARGS => $callbackArgs]);
        $this->assertEquals($callbackArgs, $cacheMessage->getCallbackArguments());
    }

    /** @covers Brickoo\Component\Cache\Message\CacheMessage::getLifetime */
    public function testGetLifetimeReturnsParameterValue() {
        $lifetime = 60;
        $cacheMessage = new CacheMessage(Messages::GET, null, [CacheMessage::PARAM_LIFETIME => $lifetime]);
        $this->assertEquals($lifetime, $cacheMessage->getLifetime());
    }

}