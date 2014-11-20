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

namespace Brickoo\Component\Cache\Adapter;

use Brickoo\Component\Validation\Argument;

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
        $this->cacheCompression = $useCompression ? (defined("MEMCACHE_COMPRESSED") ? MEMCACHE_COMPRESSED : 2) : 0;
    }

    /** {@inheritDoc} */
    public function get($identifier) {
        Argument::isString($identifier);
        return $this->memcache->get($identifier);
    }

    /** {@inheritDoc} */
    public function set($identifier, $content, $lifetime) {
        Argument::isString($identifier);
        Argument::isInteger($lifetime);
        $this->memcache->set($identifier, $content, $this->cacheCompression, $lifetime);
        return $this;
    }

    /** {@inheritDoc} */
    public function delete($identifier) {
        Argument::isString($identifier);
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
