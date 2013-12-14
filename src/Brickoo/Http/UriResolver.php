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

    namespace Brickoo\Http\Request\Factory\Resolver;

    /**
     * UriResolver
     *
     * Implements a resolver for the uri factory.
     * WARNING: This implementation has not an explicit interface as a contract,
     * the public interfaces may change in the future !!!
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UriResolver {

        /** @var \Brickoo\Http\Message\Interfaces\Header */
        private $Header;

        /** @var array */
        private $serverValues;

        /**
         * Class constructor.
         * @param \Brickoo\Http\Message\Interfaces\Header $Header
         * @param array $serverValues the server variables as key=>values pairs
         * @return void
         */
        public function __construct(\Brickoo\Http\Message\Interfaces\Header $Header, array $serverValues = null) {
            $this->Header = $Header;
            $this->serverValues = $serverValues !== null ? $serverValues : $_SERVER;
        }

        /**
         * Returns the request scheme.
         * @return string the request scheme
         */
        public function getScheme() {
            $isSecure = false;

            if ($httpsForwarded = $this->Header->get("X-Forwarded-Proto")) {
                $isSecure = (strtolower($httpsForwarded) == "https");
            }
            elseif ($secureMode = $this->getServerVar("HTTPS")) {
                $isSecure = (! empty($secureMode)) && (strtolower($secureMode) != "off" && $secureMode != "0");
            }

            return "http". ($isSecure ? "s" : "");
        }

        /**
         * Returns the request available host name or address.
         * @return string the request host
         */
        public function getHostname() {
            if ((! $host =  $this->Header->get("Host")) && (! $host = $this->getServerVar("SERVER_NAME"))) {
                $host = $this->getServerVar("SERVER_ADDR", "unknown");
            }

            return $host;
        }

        /**
         * Returns the request request port.
         * @return integer the request port
         */
        public function getPort() {
            if (! $port = $this->Header->get("X-Forwarded-Port")) {
                $port = $this->getServerVar("SERVER_PORT", 80);
            }

            return (int)$port;
        }

        /**
         * Returns the request uri path.
         * @return string the request uri path
         */
        public function getPath() {
            if ((! $requestPath = $this->getServerVar("PATH_INFO"))
                && (! $requestPath = $this->getIISRequestUri())
            ){
                $requestPath = $this->getServerVar("REQUEST_URI", "/");
            }

            return "/". trim(rawurldecode(parse_url($requestPath, PHP_URL_PATH)), "/");
        }

        /**
         * Returns the request uri query string or one built from the $_GET paramater.
         * @return string the request query string
         */
        public function getQueryString() {
            if (! $queryString = $this->getServerVar("QUERY_STRING")) {
                $queryArray = array();
                foreach ($_GET as $key => $value) {
                    $queryArray[] = $key ."=". rawurlencode($value);
                }
                $queryString = implode("&", $queryArray);
            }

            return $queryString;
        }

        /**
         * Returns the request uri fragment.
         * Since the fragment is not sent to the server
         * and using rewrite rules would be extracted,
         * just return an empty string.
         * @return string the uri fragment
         */
        public function getFragment() {
            return "";
        }

        /**
         * Returns the IIS request ur assigned if available.
         * @return string the request uri
         */
        private function getIISRequestUri() {
            if (! $requestPath = $this->Header->get("X-Original-Url")) {
                $requestPath = $this->Header->get("X-Rewrite-Url");
            }

            return $requestPath;
        }

        /**
         * Returns a PHP server variable or the default value.
         * @param string $key the key of the server variable
         * @param string $defaultValue the default value to return
         * @return string|mixed the value of the server variable otherwise the default value
         */
        private function getServerVar($key, $defaultValue = null) {
            if (! isset($this->serverValues[$key])) {
                return $defaultValue;
            }

            return $this->serverValues[$key];
        }

    }