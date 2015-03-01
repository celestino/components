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

use Brickoo\Component\Common\Assert;

/**
 * SocketStreamConfig
 *
 * Implements a socket stream configuration.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SocketStreamConfig {

    /** @var string */
    private $serverAddress;

    /** @var integer */
    private $serverPort;

    /** @var integer */
    private $connectionTimeout;

    /** @var integer */
    private $connectionType;

    /** @var array */
    private $context;

    /**
     * Class constructor.
     * @param string $address
     * @param integer $port
     * @param integer $timeout
     * @param integer $connectionType
     * @param array $context
     */
    public function __construct($address, $port, $timeout = 30, $connectionType = STREAM_CLIENT_CONNECT, array $context = array()) {
        Assert::isString($address);
        Assert::isInteger($timeout);
        Assert::isInteger($connectionType);

        $this->serverAddress = $address;
        $this->serverPort = $port;
        $this->connectionTimeout = $timeout;
        $this->connectionType = $connectionType;
        $this->context = $context;
    }

    /**
     * Returns the server address.
     * @return string the server address
     */
    public function getAddress() {
        return $this->serverAddress;
    }

    /**
     * Returns the server port number.
     * @return integer the server port number
     */
    public function getPort() {
        return $this->serverPort;
    }

    /**
     * Returns the complete socket address.
     * @return string the socket address
     */
    public function getSocketAddress() {
        return sprintf("%s:%d", $this->getAddress(), $this->getPort());
    }

    /**
     * Returns the connection timeout.
     * @return integer the connection timeout
     */
    public function getConnectionTimeout() {
        return $this->connectionTimeout;
    }

    /**
     * Return one of the connection type flags.
     * @return integer the connection type
     */
    public function getConnectionType() {
        return $this->connectionType;
    }

    /**
     * Returns the connection context.
     * @return array the connection context
     */
    public function getContext() {
        return $this->context;
    }

}
