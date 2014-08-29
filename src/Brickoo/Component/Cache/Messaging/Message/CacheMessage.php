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

namespace Brickoo\Component\Cache\Messaging\Message;

use Brickoo\Component\Messaging\GenericMessage;
use Brickoo\Component\Validation\Argument;

/**
 * CacheMessage
 *
 * Implements a cache message definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CacheMessage extends GenericMessage {

    /**
     * Cache message identifier parameter.
     * @var string
     */
    const PARAM_IDENTIFIER = "id";

    /**
     * Cache message content parameter
     * @var string
     */
    const PARAM_CONTENT = "content";

    /**
     * Cache message callback parameter.
     * @var string
     */
    const PARAM_CALLBACK = "callback";

    /**
     * Cache message callback arguments parameter.
     * @var string
     */
    const PARAM_CALLBACK_ARGS = "callbackArguments";

    /**
     * Cache message content lifetime parameter.
     * @var string
     */
    const PARAM_LIFETIME = "lifetime";

    /**
     * Set the cache message identifier.
     * @param string $identifier
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Cache\Messaging\Message\CacheMessage
     */
    public function setIdentifier($identifier) {
        Argument::isString($identifier);
        $this->setParam(self::PARAM_IDENTIFIER, $identifier);
        return $this;
    }

    /**
     * Return the cache identifier.
     * @return string the cache identifier
     */
    public function getIdentifier() {
        return $this->getParam(self::PARAM_IDENTIFIER);
    }

    /**
     * Set the cache message content to cache.
     * @param mixed $content
     * @return \Brickoo\Component\Cache\Messaging\Message\CacheMessage
     */
    public function setContent($content) {
        $this->setParam(self::PARAM_CONTENT, $content);
        return $this;
    }

    /**
     * Return the content to cache.
     * @return mixed the content to cache
     */
    public function getContent() {
        return $this->getParam(self::PARAM_CONTENT);
    }

    /**
     * Set the retrieve fresh copy callback.
     * @param callable $callback
     * @return \Brickoo\Component\Cache\Messaging\Message\CacheMessage
     */
    public function setCallback(callable $callback) {
        $this->setParam(self::PARAM_CALLBACK, $callback);
        return $this;
    }

    /**
     * Return the callback for fresh content pull.
     * @return callable
     */
    public function getCallback() {
        return $this->getParam(self::PARAM_CALLBACK);
    }

    /**
     * Set the callback arguments.
     * @param array $arguments
     * @return \Brickoo\Component\Cache\Messaging\Message\CacheMessage
     */
    public function setCallbackArguments(array $arguments) {
        $this->setParam(self::PARAM_CALLBACK_ARGS, $arguments);
        return $this;
    }

    /**
     * Returns the callback arguments needed to be passed.
     * @return array the callback arguments
     */
    public function getCallbackArguments() {
        return $this->getParam(self::PARAM_CALLBACK_ARGS);
    }

    /**
     * Set the cache content lifetime.
     * @param integer $lifetime
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Cache\Messaging\Message\CacheMessage
     */
    public function setLifetime($lifetime) {
        Argument::isInteger($lifetime);
        $this->setParam(self::PARAM_LIFETIME, $lifetime);
        return $this;
    }

    /**
     * Returns the maximum cache lifetime for fresh content pulls.
     * @return integer the cache lifetime
     */
    public function getLifetime() {
        return $this->getParam(self::PARAM_LIFETIME);
    }

}
