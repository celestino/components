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

use Brickoo\Component\Cache\Messaging\Messages;

/**
 * RetrieveByCallbackMessage
 *
 * Implements a message for retrieving cached data
 * using a callback as fresh fallback loader.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class RetrieveByCallbackMessage extends CacheMessage {

    /**
     * @param string $identifier the cache unique identifier
     * @param callable $callback the callback for fresh loading
     * @param array $callbackArguments the callback arguments
     * @param integer $cacheLifetime the max. cache lifetime for the fresh loaded content
     */
    public function __construct($identifier, callable $callback, array $callbackArguments = [], $cacheLifetime = 0) {
        parent::__construct(Messages::CALLBACK);
        $this->setIdentifier($identifier)
            ->setLifetime($cacheLifetime)
            ->setCallback($callback)
            ->setCallbackArguments($callbackArguments);
    }

}
