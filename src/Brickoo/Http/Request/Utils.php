<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Brickoo\Http\Request;

    /**
     * Utils
     *
     * Implements a collection of utils methods related to the http request.
     * WARNING: This implementation has not an explicit interface as a contract,
     * the public interfaces may change in the future !!!
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Utils {

        /** @var \Brickoo\Http\Interfaces\Request */
        private $Request;

        /**
         * Class constructor.
         * @param \Brickoo\Http\Interfaces\Request $Request
         * @return void
         */
        public function __construct(\Brickoo\Http\Interfaces\Request $Request) {
            $this->Request = $Request;
        }

        /**
         * Returns the client ip adress.
         * Passing a list of reverse proxys,
         * a deeper look into the request headers will be made.
         * @param array $proxyServers the reverse proxys to recognize
         * @return string the client ip otherwise null
         */
        public function getClientIp(array $proxyServers = null) {
            $remoteAddressIsFromReversProxy = (
                ($remoteAddress = $this->Request->getServerVar("REMOTE_ADDR"))
                && in_array($remoteAddress, $proxyServers)
            );

            if ($remoteAddressIsFromReversProxy) {
                if(($forwardedIp = $this->getForwardedClientIp()) !== null) {
                    return $forwardedIp;
                }

                if (($headerClientIp = $this->Request->getHeader()->get("Client-Ip")) &&
                    filter_var($headerClientIp, FILTER_VALIDATE_IP)
                ){
                    return $headerClientIp;
                }
            }

            return $remoteAddress;
        }

        /**
         * Returns the forwarded client ip.
         * @return string the forwarded client ip otherwise null
         */
        private function getForwardedClientIp() {
            $clientIp = null;

            if ($forwardedIps = $this->Request->getHeader()->get("X-Forwarded-For")) {
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
         * Checks if the request is using the https scheme.
         * @return boolean check result
         */
        public function isSecureConnection() {
            if ($httpsForwarded = $this->Request->getHeader()->get("X-Forwarded-Proto")) {
                return (strtolower($httpsForwarded) == "https");
            }

            $secureMode = $this->Request->getServerVar("HTTPS");
            return ($secureMode !== null) && (strtolower($secureMode) != "off" && $secureMode != "0");
        }

    }