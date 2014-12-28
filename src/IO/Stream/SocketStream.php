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
