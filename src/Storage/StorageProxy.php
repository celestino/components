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

namespace Brickoo\Component\Storage;

use Brickoo\Component\Storage\Adapter\Adapter;
use Brickoo\Component\Storage\Adapter\AdapterPoolIterator;
use Brickoo\Component\Storage\Exception\AdapterNotFoundException;
use Brickoo\Component\Validation\Argument;

/**
 * StorageProxy
 *
 * Implements caching proxy using a cache adapter pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StorageProxy {

    /** @var \Brickoo\Component\Storage\Adapter\Adapter */
    private $adapter;

    /** @var \Brickoo\Component\Storage\Adapter\AdapterPoolIterator */
    private $adapterPoolIterator;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Storage\Adapter\AdapterPoolIterator $adapterPoolIterator
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
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
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
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
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
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Storage\StorageProxy
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
     * @return \Brickoo\Component\Storage\StorageProxy
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
     * @return \Brickoo\Component\Storage\StorageProxy
     */
    public function flush() {
        $this->executeIterationCallback(function(Adapter $readyAdapter) {
            $readyAdapter->flush();
        });
        return $this;
    }

    /**
     * Returns a ready adapter entry from the adapter pool.
     * @return \Brickoo\Component\Storage\Adapter\Adapter
     */
    private function getAdapter() {
        if ($this->adapter === null) {
            $this->adapter = $this->getReadyAdapter();
        }
        return $this->adapter;
    }

    /**
     * Returns a ready adapter.
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Storage\Adapter\Adapter
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
     * @return \Brickoo\Component\Storage\StorageProxy
     */
    private function executeIterationCallback(\Closure $callbackFunction) {
        $this->rewindAdapterPool();

        while ($this->adapterPoolIterator->valid()
            && $this->adapterPoolIterator->isCurrentReady()) {
                $callbackFunction($this->adapterPoolIterator->current());
                $this->adapterPoolIterator->next();
        }
        return $this;
    }

    /**
     * Rewind the adapter pool.
     * @return \Brickoo\Component\Storage\StorageProxy
     */
    private function rewindAdapterPool() {
        $this->adapterPoolIterator->rewind();
        return $this;
    }

}
