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

/**
 * Adapter
 *
 * Defines a storage adapter.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
interface Adapter {

    /**
     * Returns the stored content from the matching identifier.
     * @param string $identifier the identifier to retrieve the content from
     * @throws \InvalidArgumentException if an argument is not valid
     * @return mixed the stored content or boolean false on failure
     */
    public function get($identifier);

    /**
     * Sets the content hold by the given identifier.
     * If the identifier already exists the content will be replaced.
     * @param string $identifier the identifier which should hold the content
     * @param mixed $content the content which should be stored
     * @param integer $lifetime the lifetime of the stored content in seconds
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Storage\Adapter\Adapter
     */
    public function set($identifier, $content, $lifetime);

    /**
     * Deletes the stored content hold by the identifier.
     * @param string $identifier the content identifier to remove
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Storage\Adapter\Adapter
     */
    public function delete($identifier);

    /**
     * Flushes the stored values by removing (or flag as removed) any content hold.
     * @return \Brickoo\Component\Storage\Adapter\Adapter
     */
    public function flush();

    /**
     * Checks if the adapter is ready.
     * @return boolean check result
     */
    public function isReady();

}
