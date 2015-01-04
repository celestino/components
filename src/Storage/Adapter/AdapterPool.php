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
 * AdapterPool
 *
 * Defines an adapter pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
interface AdapterPool {

    /**
     * Selects a pool entry by its identifier.
     * @param string $identifier the pool entry identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Storage\Adapter\Exception\PoolIdentifierDoesNotExistException
     * @return \Brickoo\Component\Storage\Adapter\AdapterPool
     */
    public function select($identifier);

    /**
     * Checks if a pool adapter entry exists.
     * @param string $identifier the pool adapter identifier
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function has($identifier);

    /**
     * Removes a pool adapter entry by its identifier.
     * @param string $identifier the pool adapter identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Storage\Adapter\Exception\PoolIdentifierDoesNotExistException
     * @return \Brickoo\Component\Storage\Adapter\AdapterPool
     */
    public function remove($identifier);

    /**
     * Checks if the pool has none entries.
     * @return boolean check result
     */
    public function isEmpty();

}
