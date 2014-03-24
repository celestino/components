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

use Brickoo\Component\Cache\Adapter,
    Brickoo\Component\Validation\Argument;

/**
 * MemcacheAdapter
 *
 * Provides an adapter for caching operations based on memcache.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MemcacheAdapter implements Adapter {

    /** @var \Memcache */
    private $memcache;

    /** @var integer */
    private $cacheCompression;

    /**
     * Class constructor.
     * @param \Memcache $memcache the Memcache dependency to inject
     * @param boolean $useCompression the compression control flag
     * @throws \InvalidArgumentException if an argument is not valid
     */
    public function __construct(\Memcache $memcache, $useCompression = false) {
        $this->memcache = $memcache;
        $this->cacheCompression = $useCompression ? (defined("MEMCACHE_COMPRESSED") ? MEMCACHE_COMPRESSED : 2) : false;
    }

    /** {@inheritDoc} */
    public function get($identifier) {
        Argument::IsString($identifier);
        return $this->memcache->get($identifier);
    }

    /** {@inheritDoc} */
    public function set($identifier, $content, $lifetime) {
        Argument::IsString($identifier);
        Argument::IsInteger($lifetime);
        $this->memcache->set($identifier, $content, $this->cacheCompression, $lifetime);
        return $this;
    }

    /** {@inheritDoc} */
    public function delete($identifier) {
        Argument::IsString($identifier);
        $this->memcache->delete($identifier);
        return $this;
    }

    /** {@inheritDoc} */
    public function flush() {
        $this->memcache->flush();
        return $this;
    }

    /** {@inheritDoc} */
    public function isReady() {
        return extension_loaded("memcache");
    }

    /**
     * Magic function to call other Memcache methods not implemented.
     * @param string $method the method called
     * @param array $arguments the arguments passed
     * @throws \BadMethodCallException if the method is not defined
     * @return mixed the called Memcache method result
     */
    public function __call($method, array $arguments) {
        if (! method_exists($this->memcache, $method)) {
            throw new \BadMethodCallException(sprintf("The memcache method `%s` is not defined.", $method));
        }
        return call_user_func_array([$this->memcache, $method], $arguments);
    }

}