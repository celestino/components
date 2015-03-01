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

namespace Brickoo\Component\Storage\Adapter;

use Brickoo\Component\Common\Assert;

/**
 * MemoryAdapter
 *
 * Implements a memory cache adapter for handling runtime cache operations.
 * Currently the cached content does not expire.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class MemoryAdapter implements Adapter {

    /** @var array */
    private $cacheValues;

    public function __construct() {
        $this->cacheValues = [];
    }

    /** {@inheritDoc} */
    public function get($identifier) {
        Assert::isString($identifier);
        if (!array_key_exists($identifier, $this->cacheValues)) {
            return null;
        }
        return $this->cacheValues[$identifier];
    }

    /** {@inheritDoc} */
    public function set($identifier, $content, $lifetime = 0) {
        Assert::isString($identifier);
        $this->cacheValues[$identifier] = $content;
        return $this;
    }

    /** {@inheritDoc} */
    public function delete($identifier) {
        Assert::isString($identifier);
        if (array_key_exists($identifier, $this->cacheValues)) {
            unset($this->cacheValues[$identifier]);
        }
        return $this;
    }

    /** {@inheritDoc} */
    public function has($identifier) {
        return array_key_exists($identifier, $this->cacheValues);
    }

    /** {@inheritDoc} */
    public function flush() {
        $this->cacheValues = [];
        return $this;
    }

    /** {@inheritDoc} */
    public function isReady() {
        return true;
    }

}
