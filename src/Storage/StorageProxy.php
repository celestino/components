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
 * Implements a storage proxy using an adapter pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class StorageProxy {

    /** @var string */
    const BREAK_ITERATION_CALLBACK = "BIC";

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
     * Returns a stored content or if the stored content is not available,
     * it will be retrieved by the provided callback and stored back into the storage.
     * @param string $identifier the identifier to retrieve/store the content from/to
     * @param callable $callback the callback to call if the content is not stored
     * @param array $callbackArguments the arguments to pass forward to the callback
     * @param integer $lifetime the lifetime of the stored content in seconds
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @return mixed the stored content
     */
    public function getByCallback($identifier, callable $callback, array $callbackArguments, $lifetime) {
        Argument::isString($identifier);
        Argument::isInteger($lifetime);

        if ((! ($content = $this->get($identifier)))
            && ($content = call_user_func_array($callback, $callbackArguments))) {
                $this->set($identifier, $content, $lifetime);
        }
        return $content;
    }

    /**
     * Returns the stored content hold by the identifier.
     * @param string $identifier the identifier to retrieve the content
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @return mixed the stored content
     */
    public function get($identifier) {
        Argument::isString($identifier);
        return $this->getAdapter()->get($identifier);
    }

    /**
     * Sets the content hold by the given identifier.
     * If the identifier already exists the content will be replaced.
     * @param string $identifier the identifier which holds the content
     * @param mixed $content the content to store
     * @param integer $lifetime the lifetime of the stored content
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
     * Deletes the stored content which is hold by the identifier.
     * Removes the local stored content.
     * @param string $identifier the identifier which holds the content
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Storage\StorageProxy
     */
    public function delete($identifier) {
        Argument::isString($identifier);
        $this->executeIterationCallback(
            function(Adapter $readyAdapter) use ($identifier) {
                $readyAdapter->delete($identifier);
                return null;
            }
        );
        return $this;
    }

    /**
     * Flushes the storage of all ready adapters.
     * @return \Brickoo\Component\Storage\StorageProxy
     */
    public function flush() {
        $this->executeIterationCallback(
            function(Adapter $readyAdapter) {
                $readyAdapter->flush();
                return null;
            }
        );
        return $this;
    }

    /**
     * Returns a ready adapter entry from the adapter pool.
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Storage\Adapter\Adapter
     */
    private function getAdapter() {
        if ($this->adapter === null) {
            $this->adapter = $this->getReadyAdapter();
        }
        return $this->adapter;
    }

    /**
     * Returns an adapter which is ready to use.
     * @throws \Brickoo\Component\Storage\Exception\AdapterNotFoundException
     * @return \Brickoo\Component\Storage\Adapter\Adapter
     */
    private function getReadyAdapter() {
        $adapter = null;

        $this->executeIterationCallback(
            function(Adapter $readyAdapter) use (&$adapter) {
                $adapter = $readyAdapter;
                return StorageProxy::BREAK_ITERATION_CALLBACK;
            }
        );

        if (! $adapter instanceof Adapter) {
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

        $callbackValue = null;
        while ($callbackValue !== self::BREAK_ITERATION_CALLBACK
            && $this->adapterPoolIterator->valid()
            && $this->adapterPoolIterator->isCurrentReady()) {
                $callbackValue = $callbackFunction($this->adapterPoolIterator->current());
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
