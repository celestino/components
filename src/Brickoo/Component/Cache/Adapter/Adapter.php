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

namespace Brickoo\Component\Cache\Adapter;

/**
 * Adapter
 *
 * Defines a caching adapter.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
Interface Adapter {

    /**
     * Returns the cached content from the matching identifier.
     * @param string $identifier the identifier to retrieve the content from
     * @throws \InvalidArgumentException if an argument is not valid
     * @return mixed the cached content or boolean false on failure
     */
    public function get($identifier);

    /**
     * Sets the content hold by the given identifier.
     * If the identifier already exists the content will be replaced.
     * @param string $identifier the identifier which should hold the content
     * @param mixed $content the content which should be cached
     * @param integer $lifetime the lifetime of the cached content in seconds
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Cache\Adapter\Adapter
     */
    public function set($identifier, $content, $lifetime);

    /**
     * Deletes the cached content hold by the identifier.
     * @param string $identifier the content identifier to remove
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Cache\Adapter\Adapter
     */
    public function delete($identifier);

    /**
     * Flushes the cached values by removing (or flag as removed) any content hold.
     * @return \Brickoo\Component\Cache\Adapter\Adapter
     */
    public function flush();

    /**
     * Checks if the adapter is ready.
     * @return boolean check result
     */
    public function isReady();

}
