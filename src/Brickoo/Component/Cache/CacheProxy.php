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

use Brickoo\Component\Cache\Adapter\Adapter;
use Brickoo\Component\Cache\Adapter\AdapterPoolIterator;
use Brickoo\Component\Cache\Exception\AdapterNotFoundException;
use Brickoo\Component\Validation\Argument;

/**
 * CacheProxy
 *
 * Implements caching proxy using a cache adapter pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CacheProxy {

    /** @var \Brickoo\Component\Cache\Adapter\Adapter */
    private $adapter;

    /** @var \Brickoo\Component\Cache\Adapter\AdapterPoolIterator */
    private $adapterPoolIterator;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Cache\Adapter\AdapterPoolIterator $adapterPoolIterator
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
        Argument::isString($identifier);
        Argument::isInteger($lifetime);

        if ((!$content = $this->get($identifier)) && ($content = call_user_func_array($callback, $callbackArguments))) {
            $this->set($identifier, $content, $lifetime);
        }
        return $content;
    }

    /**
     * Returns the cached content hold by the identifier.
     * @param string $identifier the identifier to retrieve the content
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @return mixed the cached content
     */
    public function get($identifier) {
        Argument::isString($identifier);
        return $this->getAdapter()->get($identifier);
    }

    /**
     * Sets the content hold by the given identifier.
     * If the identifier already exists the content will be replaced.
     * @param string $identifier the identifier which holds the content
     * @param mixed $content the content to cache
     * @param integer $lifetime the lifetime of the cached content
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    public function set($identifier, $content, $lifetime) {
        Argument::isString($identifier);
        Argument::isInteger($lifetime);
        $this->getAdapter()->set($identifier, $content, $lifetime);
        return $this;
    }

    /**
     * Deletes the cached content which is hold by the identifier.
     * Removes the local cached content.
     * @param string $identifier the identifier which holds the content
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    public function delete($identifier) {
        Argument::isString($identifier);
        $this->executeIterationCallback(function(Adapter $readyAdapter) use ($identifier) {
            $readyAdapter->delete($identifier);
        });
        return $this;
    }

    /**
     * Flushes the cache hold by all ready adapters.
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    public function flush() {
        $this->executeIterationCallback(function(Adapter $readyAdapter) {
            $readyAdapter->flush();
        });
        return $this;
    }

    /**
     * Returns a ready adapter entry from the adapter pool.
     * @return \Brickoo\Component\Cache\Adapter\Adapter
     */
    private function getAdapter() {
        if ($this->adapter === null) {
            $this->adapter = $this->getReadyAdapter();
        }
        return $this->adapter;
    }

    /**
     * Returns a ready adapter.
     * @throws \Brickoo\Component\Cache\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Cache\Adapter\Adapter
     */
    private function getReadyAdapter() {
        $adapter = null;

        $this->executeIterationCallback(function(Adapter $readyAdapter) use (&$adapter) {
            $adapter = $readyAdapter;
        });

        if ($adapter === null) {
            throw new AdapterNotFoundException();
        }
        return $adapter;
    }

    /**
     * Execute a callback on every ready adapter.
     * @param \Closure $callbackFunction
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    private function executeIterationCallback(\Closure $callbackFunction) {
        $this->rewindAdapterPool();

        while ($this->adapterPoolIterator->valid()
            && $this->adapterPoolIterator->isCurrentReady()) {
                $callbackFunction($this->adapterPoolIterator->current());
        }
        return $this;
    }

    /**
     * Rewind the adapter pool.
     * @return \Brickoo\Component\Cache\CacheProxy
     */
    private function rewindAdapterPool() {
        $this->adapterPoolIterator->rewind();
        return $this;
    }

}
