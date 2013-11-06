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

namespace Brickoo\Cache\Event;

use Brickoo\Event\Event,
    Brickoo\Validator\Argument;

/**
 * AbstractEvent
 *
 * Implements an abstract cache event definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AbstractEvent extends Event {

    /**
     * Cache event identifier parameter.
     * @var string
     */
    const PARAM_IDENTIFIER = "id";

    /**
     * Cache event content parameter
     * @var string
     */
    const PARAM_CONTENT = "content";

    /**
     * Cache event callback parameter.
     * @var string
     */
    const PARAM_CALLBACK = "callback";

    /**
     * Cache event callback arguments parameter.
     * @var string
     */
    const PARAM_CALLBACK_ARGS = "callbackArguments";

    /**
     * Cache event content lifetime parameter.
     * @var string
     */
    const PARAM_LIFETIME = "lifetime";

    /**
     * Returns the cache identifier.
     * @return string the cache identifier
     */
    public function getIdentifier() {
        return $this->getParam(self::PARAM_IDENTIFIER);
    }

    /**
     * Returns the content to cache.
     * @return mixed the content to cache
     */
    public function getContent() {
        return $this->getParam(self::PARAM_CONTENT);
    }

    /**
     * Returns the callback for fresh content pull.
     * @return callable the callback
     */
    public function getCallback() {
        return $this->getParam(self::PARAM_CALLBACK);
    }

    /**
     * Returns the callback arguments needed to be passed.
     * @return array the callback arguments
     */
    public function getCallbackArguments() {
        return $this->getParam(self::PARAM_CALLBACK_ARGS);
    }

    /**
     * Returns the maximum cache lifetime for fresh content pulls.
     * @return integer the cache lifetime
     */
    public function getLifetime() {
        return $this->getParam(self::PARAM_LIFETIME);
    }

}