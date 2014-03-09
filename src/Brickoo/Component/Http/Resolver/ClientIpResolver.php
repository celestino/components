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

namespace Brickoo\Component\Http\Resolver;

use Brickoo\Component\Http\MessageHeader;

/**
 * ClientIpResolver
 *
 * Implements a client ip solver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ClientIpResolver {

    /** @var \Brickoo\Component\Http\MessageHeader */
    private $headers;

    /** @var array */
    private $proxyServers;

    /** @var array */
    private $serverVars;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Http\MessageHeader $headers
     * @param array $serverVars the server variables
     * @param array $proxyServers the proxies to recognize
     * @return void
     */
    public function __construct(MessageHeader $headers, array $serverVars = [], array $proxyServers =  array()) {
        $this->headers = $headers;
        $this->serverVars = $serverVars;
        $this->proxyServers = $proxyServers;
    }

    /**
     * Returns the client ip adress.
     * @return string the client ip
     */
    public function getClientIp() {
        $remoteAddressIsFromReversProxy = (
            ($remoteAddress = $this->getServerVar("REMOTE_ADDR"))
            && in_array($remoteAddress, $this->proxyServers)
        );

        if ($remoteAddressIsFromReversProxy
            && ($originalClientIp = $this->getOriginalClientIp())
        ){
            return $originalClientIp;
        }

        return $remoteAddress ?: "";
    }

    /**
     * Returns the original client ip.
     * @return string the original client ip otherwise null
     */
    private function getOriginalClientIp() {
        if(($forwardedIp = $this->getForwardedClientIp()) !== null) {
            return $forwardedIp;
        }

        if ($this->headers->hasHeader("Client-Ip")
            && ($headerClientIp = $this->headers->getHeader("Client-Ip")->getValue())
            && filter_var($headerClientIp, FILTER_VALIDATE_IP)
        ){
            return $headerClientIp;
        }
    }

    /**
     * Returns the forwarded client ip.
     * @return string the forwarded client ip otherwise null
     */
    private function getForwardedClientIp() {
        $clientIp = null;

        if ($this->headers->hasHeader("X-Forwarded-For")
            && ($forwardedIps = $this->headers->getHeader("X-Forwarded-For")->getValue())
        ){
            $forwardedIps = array_filter(
                preg_split("/[\s]*,[\s]*/", $forwardedIps),
                function($ip){return filter_var($ip, FILTER_VALIDATE_IP);}
            );

            if (! empty($forwardedIps)) {
                $clientIp = array_shift($forwardedIps);
            }
        }

        return $clientIp;
    }

    /**
     * Returns the server variable value.
     * @param stŕing $key
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