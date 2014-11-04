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
