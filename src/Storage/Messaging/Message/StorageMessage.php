<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\Storage\Messaging\Message;

use Brickoo\Component\Messaging\GenericMessage;
use Brickoo\Component\Validation\Argument;

/**
 * StorageMessage
 *
 * Implements a cache message definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StorageMessage extends GenericMessage {

    /**
     * Storage message identifier parameter.
     * @var string
     */
    const PARAM_IDENTIFIER = "id";

    /**
     * Storage message content parameter
     * @var string
     */
    const PARAM_CONTENT = "content";

    /**
     * Storage message callback parameter.
     * @var string
     */
    const PARAM_CALLBACK = "callback";

    /**
     * Storage message callback arguments parameter.
     * @var string
     */
    const PARAM_CALLBACK_ARGS = "callbackArguments";

    /**
     * Storage message content lifetime parameter.
     * @var string
     */
    const PARAM_LIFETIME = "lifetime";

    /**
     * Set the cache message identifier.
     * @param string $identifier
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Storage\Messaging\Message\StorageMessage
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
     * @return \Brickoo\Component\Storage\Messaging\Message\StorageMessage
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
     * @return \Brickoo\Component\Storage\Messaging\Message\StorageMessage
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
     * @return \Brickoo\Component\Storage\Messaging\Message\StorageMessage
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
     * @return \Brickoo\Component\Storage\Messaging\Message\StorageMessage
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
