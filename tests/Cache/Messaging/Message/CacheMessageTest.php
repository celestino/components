<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Cache\Messaging\Message;

use Brickoo\Component\Cache\Messaging\Messages;
use Brickoo\Component\Cache\Messaging\Message\CacheMessage;
use PHPUnit_Framework_TestCase;

/**
 * CacheMessageTest
 *
 * Test suite for the CacheMessage class.
 * @see Brickoo\Component\Cache\Messaging\Message\CacheMessage
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class CacheMessageTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::setIdentifier
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::getIdentifier
     */
    public function testGetIdentifierReturnsParameterValue() {
        $identifier = "some_identifier";
        $cacheMessage = new CacheMessage(Messages::GET);
        $cacheMessage->setIdentifier($identifier);
        $this->assertEquals($identifier, $cacheMessage->getIdentifier());
    }

    /**
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::setContent
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::getContent
     */
    public function testGetContentReturnsParameterValue() {
        $content = "some cached content";
        $cacheMessage = new CacheMessage(Messages::GET);
        $cacheMessage->setContent($content);
        $this->assertEquals($content, $cacheMessage->getContent());
    }

    /**
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::setCallback
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::getCallback
     */
    public function testGetCallbackReturnsParameterValue() {
        $callback = function(){};
        $cacheMessage = new CacheMessage(Messages::GET);
        $cacheMessage->setCallback($callback);
        $this->assertEquals($callback, $cacheMessage->getCallback());
    }

    /**
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::setCallbackArguments
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::getCallbackArguments
     */
    public function testGetCallbackArgumentsReturnsParameterValue() {
        $callbackArgs = ["key" => "value"];
        $cacheMessage = new CacheMessage(Messages::GET);
        $cacheMessage->setCallbackArguments($callbackArgs);
        $this->assertEquals($callbackArgs, $cacheMessage->getCallbackArguments());
    }

    /**
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::getLifetime
     * @covers Brickoo\Component\Cache\Messaging\Message\CacheMessage::setLifetime
     */
    public function testGetLifetimeReturnsParameterValue() {
        $lifetime = 60;
        $cacheMessage = new CacheMessage(Messages::GET);
        $cacheMessage->setLifetime($lifetime);
        $this->assertEquals($lifetime, $cacheMessage->getLifetime());
    }

}
