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

namespace Brickoo\Component\Cache;

use Brickoo\Component\Cache\AdapterPoolIterator,
    Brickoo\Component\Cache\Exception\AdapterNotFoundException,
    Brickoo\Component\Validation\Argument;

/**
 * CacheProxy
 *
 * Implements caching proxy for handling a cache pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CacheProxy {

    /** @var \Brickoo\Component\Cache\Adapter */
    private $adapter;

    /** @var \Brickoo\Component\Cache\AdapterPoolIterator */
    private $adapterPoolIterator;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Cache\AdapterPoolIterator $adapterPoolIterator
     * @return void
     */
    public function __construct(AdapterPoolIterator $adapterPoolIterator) {
        $this->adapter = null;
        $this->adapterPoolIterator = $adapterPoolIterator;
    }

    /**
     * Returns a cached content or if the cached content is not available,
     * it will be retrieved by the provided callback and stored back into the cache.
     * @param string $identifier the identifier to retrieve/store the content from/to
     * @param callable $callback the callback to call if the content is not cached
     * @param array $callbackArguments the arguments to pass forward to the callback
     * @param integer $lifetime the lifetime of the cached content in seconds
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @return mixed the cached content
     */
    public function getByCallback($identifier, callable $callback, array $callbackArguments, $lifetime) {
        Argument::IsString($identifier);
        Argument::IsInteger($lifetime);

        if ((!$content = $this->get($identifier)) && ($content = call_user_func_array($callback, $callbackArguments))) {
            $this->set($identifier, $content, $lifetime);
        }
        return $content;
    }

    /**
     * Returns the cached content holded by the identifier.
     * @param string $identifier the identifier to retrieve the content
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @return mixed the cached content
     */
    public function get($identifier) {
        Argument::IsString($identifier);
        return $this->getAdapter()->get($identifier);
    }

    /**
     * Sets the content holded by the given identifier.
     * If the identifer already exists the content will be replaced.
     * @param string $identifier the identifier which holds the content
     * @param mixed $content the content to cache
     * @param integer $lifetime the lifetime of the cached content
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    public function set($identifier, $content, $lifetime) {
        Argument::IsString($identifier);
        Argument::IsInteger($lifetime);
        $this->getAdapter()->set($identifier, $content, $lifetime);
        return $this;
    }

    /**
     * Deletes the cached content which is holded by the identifier.
     * Removes the local cached content.
     * @param string $identifier the identifier which holds the content
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    public function delete($identifier) {
        Argument::IsString($identifier);
        $this->adapterPoolIterator->rewind();
        while ($this->adapterPoolIterator->valid()) {
            if ($this->adapterPoolIterator->isCurrentReady()) {
                $this->adapterPoolIterator->current()->delete($identifier);
            }
            $this->adapterPoolIterator->next();
        }
        return $this;
    }

    /**
     * Flushes the cache holded by all ready adapters.
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    public function flush() {
        $this->adapterPoolIterator->rewind();
        while ($this->adapterPoolIterator->valid()) {
            if ($this->adapterPoolIterator->isCurrentReady()) {
                $this->adapterPoolIterator->current()->flush();
            }
            $this->adapterPoolIterator->next();
        }
        return $this;
    }

    /**
     * Returns a ready adapter entry from the adapter pool.
     * @return \Brickoo\Component\Cache\Adapter
     */
    private function getAdapter() {
        if ($this->adapter !== null) {
            return $this->adapter;
        }
        $this->adapter = $this->getReadyAdapter();
        return $this->adapter;
    }

    /**
     * Returns a ready adapter.
     * @throws \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Cache\Adapter
     */
    private function getReadyAdapter() {
        $adapter = null;
        $this->adapterPoolIterator->rewind();
        while ($adapter === null && $this->adapterPoolIterator->valid()) {
            if ($this->adapterPoolIterator->isCurrentReady()) {
                $adapter = $this->adapterPoolIterator->current();
            }
            $this->adapterPoolIterator->next();
        }

        if ($adapter === null) {
            throw new AdapterNotFoundException();
        }
        return $adapter;
    }

}