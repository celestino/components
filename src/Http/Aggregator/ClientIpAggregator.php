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

namespace Brickoo\Component\Http\Aggregator;

use Brickoo\Component\Http\HttpMessageHeader;

/**
 * ClientIpAggregator
 *
 * Implements a client ip aggregator.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ClientIpAggregator {

    /** @var \Brickoo\Component\Http\HttpMessageHeader */
    private $httpHeader;

    /** @var array */
    private $proxyServers;

    /** @var array */
    private $serverVars;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Http\HttpMessageHeader $headers
     * @param array $serverVars the server variables
     * @param array $proxyServers the proxies to recognize
     */
    public function __construct(HttpMessageHeader $headers, array $serverVars = [], array $proxyServers = []) {
        $this->httpHeader = $headers;
        $this->serverVars = $serverVars;
        $this->proxyServers = $proxyServers;
    }

    /**
     * Returns the client ip address.
     * @return null|string
     */
    public function getClientIp() {
        if (($remoteAddress = $this->getServerVar("REMOTE_ADDR")) !== null
            && (!in_array($remoteAddress, $this->proxyServers))) {
            return $remoteAddress;
        }

        if ($originalClientIp = $this->getOriginalClientIp()) {
            return $originalClientIp;
        }

        return null;
    }

    /**
     * Return the original client ip.
     * @return null|string
     */
    private function getOriginalClientIp() {
        if (($forwardedIp = $this->getForwardedClientIp()) !== null) {
            return $forwardedIp;
        }
        return $this->getClientIpFromHeaderField();
    }

    /**
     * Return the client ip from the message header fields.
     * @return null|string
     */
    private function getClientIpFromHeaderField() {
        if ($this->httpHeader->contains("Client-Ip")
            && ($headerClientIp = $this->httpHeader->getField("Client-Ip")->getValue())
            && filter_var($headerClientIp, FILTER_VALIDATE_IP)) {
                return $headerClientIp;
        }
        return null;
    }

    /**
     * Return the forwarded client ip.
     * @return null|string
     */
    private function getForwardedClientIp() {
        $clientIp = null;

        if ($this->httpHeader->contains("X-Forwarded-For")
            && ($forwardedIps = $this->httpHeader->getField("X-Forwarded-For")->getValue())) {

            $forwardedIps = array_filter(
                preg_split("/[\\s]*,[\\s]*/", $forwardedIps),
                function($ipToValidate) {
                    return filter_var($ipToValidate, FILTER_VALIDATE_IP);
                }
            );

            if (!empty($forwardedIps)) {
                $clientIp = array_shift($forwardedIps);
            }
        }

        return $clientIp;
    }

    /**
     * Return the server variable value
     * or the default value if not available.
     * @param string $key
     * @param mixed $defaultValue
     * @return string
     */
    private function getServerVar($key, $defaultValue = null) {
        if (isset($this->serverVars[$key])) {
            return $this->serverVars[$key];
        }
        return $defaultValue;
    }

}
