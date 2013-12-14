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

use Brickoo\Cache\Event\CacheEvent,
    Brickoo\Cache\Event\Events,
    Brickoo\Validator\Argument;

/**
 * StoreEvent
 *
 * Implements a content caching event.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class StoreEvent extends CacheEvent {

    /**
     * Overrides the parent class constructor.
     * Calls the parent constructor.
     * @param string $identifier the cache content identifier
     * @paran mixed $content the content to cache
     * @param integer $cacheLifetime the maix. cache lifetime for the content
     * @return void
     */
    public function __construct($identifier, $content, $cacheLifetime = 60) {
        Argument::IsString($identifier);
        Argument::IsInteger($cacheLifetime);
        parent::__construct(Events::SET, null, array(
            self::PARAM_IDENTIFIER => $identifier,
            self::PARAM_CONTENT => $content,
            self::PARAM_LIFETIME => $cacheLifetime
        ));
    }

}