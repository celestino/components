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

namespace Brickoo\Component\IO\Stream;

use Brickoo\Component\IO\Stream\Exception\UnableToOpenStreamException;

/**
 * SocketStream
 *
 * Implements a handler for creating socket based streams.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SocketStream implements Stream {

    /** @var \Brickoo\Component\IO\Stream\SocketStreamConfig */
    private $streamConfig;

    /** @var resource */
    private $resource;

    /** @param SocketStreamConfig $streamConfig */
    public function __construct(SocketStreamConfig $streamConfig) {
        $this->streamConfig = $streamConfig;
    }

    /**
     * Returns the socket stream configuration.
     * @return SocketStreamConfig
     */
    public function getConfiguration() {
        return $this->streamConfig;
    }

    /**
     * Reconfigure the socket stream context.
     * @param SocketStreamConfig $streamConfig
     * @return \Brickoo\Component\IO\Stream\SocketStream
     */
    public function reconfigure(SocketStreamConfig $streamConfig) {
        $this->streamConfig = $streamConfig;
        return $this;
    }

    /** {@inheritdoc} */
    public function open() {
        if ($this->hasResource()) {
            return $this->resource;
        }

        $errorCode = null;
        $errorMessage = null;
        $configuration = $this->getConfiguration();

        if (! ($resource = @stream_socket_client(
            $configuration->getSocketAddress(),
            $errorCode, $errorMessage,
            $configuration->getConnectionTimeout(),
            $configuration->getConnectionType(),
            stream_context_create($configuration->getContext())
        ))) {
            throw new UnableToOpenStreamException($errorMessage, $errorCode);
        }

        $this->resource = $resource;
        return $resource;
    }

    /** {@inheritdoc} */
    public function close() {
        if ($this->hasResource()) {
            fclose($this->resource);
            $this->resource = null;
        }
    }

    /** Release stream resource on destruction. */
    public function __destruct() {
        $this->close();
    }

    /**
     * Checks if the resource has been already created.
     * @return boolean check result
     */
    private function hasResource() {
        return is_resource($this->resource);
    }
}
