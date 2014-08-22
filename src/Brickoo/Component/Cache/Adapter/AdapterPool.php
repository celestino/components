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
 * AdapterPool
 *
 * Defines an adapter pool.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
interface AdapterPool {

    /**
     * Selects a pool entry by its identifier.
     * @param string|integer $identifier the pool entry identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Cache\Adapter\Exception\PoolIdentifierDoesNotExistException
     * @return \Brickoo\Component\Cache\Adapter\AdapterPool
     */
    public function select($identifier);

    /**
     * Checks if a pool adapter entry exists.
     * @param string|integer $identifier the pool adapter identifier
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function has($identifier);

    /**
     * Removes a pool adapter entry by its identifier.
     * @param string|integer $identifier the pool adapter identifier
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Cache\Adapter\Exception\PoolIdentifierDoesNotExistException
     * @return \Brickoo\Component\Cache\Adapter\AdapterPool
     */
    public function remove($identifier);

    /**
     * Checks if the pool has none entries.
     * @return boolean check result
     */
    public function isEmpty();

}
