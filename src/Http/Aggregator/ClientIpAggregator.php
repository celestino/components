<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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
    private $headers;

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
        $this->headers = $headers;
        $this->serverVars = $serverVars;
        $this->proxyServers = $proxyServers;
    }

    /**
     * Returns the client ip address.
     * @return string the client ip
     */
    public function getClientIp() {
        if (($remoteAddress = $this->getServerVar("REMOTE_ADDR", ""))
            && in_array($remoteAddress, $this->proxyServers)
            && ($originalClientIp = $this->getOriginalClientIp())) {
            return $originalClientIp;
        }
        return $remoteAddress;
    }

    /**
     * Return the original client ip.
     * @return string|null the original client ip otherwise null
     */
    private function getOriginalClientIp() {
        if (($forwardedIp = $this->getForwardedClientIp()) !== null) {
            return $forwardedIp;
        }
        return $this->getClientIpFromHeaders();
    }

    /**
     * Return the client ip from the message headers.
     * @return null|string
     */
    private function getClientIpFromHeaders() {
        if ($this->headers->contains("Client-Ip")
            && ($headerClientIp = $this->headers->getHeader("Client-Ip")->getValue())
            && filter_var($headerClientIp, FILTER_VALIDATE_IP)) {
            return $headerClientIp;
        }
        return null;
    }

    /**
     * Return the forwarded client ip.
     * @return string the forwarded client ip otherwise null
     */
    private function getForwardedClientIp() {
        $clientIp = null;

        if ($this->headers->contains("X-Forwarded-For")
            && ($forwardedIps = $this->headers->getHeader("X-Forwarded-For")->getValue())) {

            $forwardedIps = array_filter(
                preg_split("/[\\s]*,[\\s]*/", $forwardedIps),
                function($ipToValidate) {
                    return filter_var($ipToValidate, FILTER_VALIDATE_IP);
                }
            );

            if (! empty($forwardedIps)) {
                $clientIp = array_shift($forwardedIps);
            }
        }

        return $clientIp;
    }

    /**
     * Return the server variable value.
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed the server variable value otherwise the default value
     */
    private function getServerVar($key, $defaultValue = null) {
        if (isset($this->serverVars[$key])) {
            return $this->serverVars[$key];
        }
        return $defaultValue;
    }

}
