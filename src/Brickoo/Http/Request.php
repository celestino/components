<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Brickoo\Http;

    use Brickoo\Memory,
        Brickoo\Validator\TypeValidator;

    /**
     * Request
     *
     * Implements methods to create or handle a http request.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Request implements Interfaces\Request {

        /**
         * Holds the class dependencies.
         * @var array
         */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependency the dependecy to inject
         * @return object Request if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependency = null) {
            if ($Dependency instanceof $interface) {
                $this->dependencies[$name] = $Dependency;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the Url dependency
         * @param \Brickoo\Http\Component\Interfaces\Url $Url the Url dependency to inject
         * @return \Brickoo\Http\Interfaces\Url
         */
        public function Url(\Brickoo\Http\Component\Interfaces\Url $Url = null) {
            return $this->getDependency(
                'Url',
                '\Brickoo\Http\Component\Interfaces\Url',
                function($Request) {
                    $Url = new Component\Url();
                    $Url->Request($Request);
                    return $Url;
                },
                $Url
            );
        }

        /**
         * Lazy initialization of the Headers dependency.
         * @param \Brickoo\Http\Component\Interfaces\Headers $Headers the Headers dependency to inject
         * @return \Brickoo\Http\Component\Interfaces\Headers
         */
        public function Headers(\Brickoo\Http\Component\Interfaces\Headers $Headers = null) {
            return $this->getDependency(
                'Headers',
                '\Brickoo\Http\Component\Interfaces\Headers',
                function() {return new Component\Headers();},
                $Headers
            );
        }

        /**
         * Lazy initialization of the Query dependency.
         * @param \Brickoo\Http\Component\Interfaces\Query $Query the Query dependency to inject
         * @return \Brickoo\Http\Component\Interfaces\Query
        */
        public function Query(\Brickoo\Http\Component\Interfaces\Query $Query = null) {
            return $this->getDependency(
                'Query',
                '\Brickoo\Http\Component\Interfaces\Query',
                function() {return new Component\Query();},
                $Query
            );
        }

        /**
         * Lazy initialization of the Post dependency.
         * @param \Brickoo\Memory\Interfaces\Container $Post the Post dependency to inject
         * @return \Brickoo\Memory\Interfaces\Container
         */
        public function Post(\Brickoo\Memory\Interfaces\Container $Post = null) {
            return $this->getDependency(
                'Post',
                '\Brickoo\Memory\Interfaces\Container',
                function(){return new Memory\Container();},
                $Post
            );
        }

        /**
         * Lazy initialization of the Files dependency.
         * @param \Brickoo\Memory\Interfaces\Container $Files the Files dependency to inject
         * @return \Brickoo\Memory\Interfaces\Container
         */
        public function Files(\Brickoo\Memory\Interfaces\Container $Files = null) {
            return $this->getDependency(
                'Files',
                '\Brickoo\Memory\Interfaces\Container',
                function(){return new Memory\Container();},
                $Files
            );
        }

        /**
         * Holds the request protocol HTTP/1.(0|1).
         * @var string
         */
        protected $protocol;

        /**
         * Returns the request protocol.
         * Lazy value set if the protocol is not set.
         * @return string the request protocol
         */
        public function getProtocol() {
            if ($this->protocol === null) {
                $this->protocol = $this->getServerVar('SERVER_PROTOCOL');
            }

            return $this->protocol;
        }

        /**
         * Sets the request protocol used.
         * @param string $protocol the protocol to set.
         * @return \Brickoo\Http\Request
         */
        public function setProtocol($protocol) {
            TypeValidator::IsStringAndNotEmpty($protocol);
            TypeValidator::MatchesRegex('~^HTTP/1\.[0|1]$~', $protocol);

            $this->protocol = $protocol;

            return $this;
        }

        /**
         * Holds the http request method.
         * @var string
         */
        protected $method;

        /**
         * Returns the request method.
         * Fallback if the method is not supported to standard GET.
         * @return string the http request method
         */
        public function getMethod() {
            if ($this->method === null) {
                $this->setMethod($this->getServerVar('REQUEST_METHOD', 'GET'));
            }

            return $this->method;
        }

        /**
         * Sets the http request method.
         * @param string $method the http request method
         * @throws Exceptions\MethodNotSupported if the method is not supported
         * @return \Brickoo\Http\Request
         */
        public function setMethod($method) {
            TypeValidator::IsStringAndNotEmpty($method);

            $this->method = strtoupper($method);

            return $this;
        }

        /**
         * Returns the server hostname.
         * @return the server hostname
         */
        public function getHost() {
            return $this->Url()->getHost();
        }

        /**
         * Returns the request path.
         * @return string the request path
         */
        public function getPath() {
            return $this->Url()->getPath();
        }

        /**
         * Returns the request format.
         * @return string the request format or null if not set
         */
        public function getFormat() {
            return $this->Url()->getFormat();
        }

        /**
         * Class constructor.
         * Initializes class properties.
         * @return void
         */
        public function __construct() {
            $this->method              = null;
            $this->dependencies        = array();
        }

        /**
         * Imports the global request variables.
         * @return \Brickoo\Http\Request
         */
        public function importFromGlobals() {
            $this->Url()->importFromGlobals();
            $this->Query()->importFromGlobals();
            $this->Headers()->importFromGlobals();
            $this->Post()->merge($_POST);
            $this->Files()->merge($_FILES);

            return $this;
        }

        /**
         * Returns the server variable value if available.
         * @param string $name the server variable name to retrieve the value from
         * @param mixed $defaultValue the default value to return
         * @return string the value of the server variable otherwise mixed the default value
         */
        public function getServerVar($name, $defaultValue = null) {
            TypeValidator::IsStringAndNotEmpty($name);

            if (isset($_SERVER[$name])) {
                return $_SERVER[$name];
            }

            return $defaultValue;
        }

        /**
         * Returns the client ip adress.
         * Passing a list of reverse proxys,
         * a deeper look into the request headers will be made.
         * @param array $proxyServers the reverse proxys to recognize
         * @return string the client ip or null if not available
         */
        public function getClientIp(array $proxyServers = array()) {
            $remoteAddressIsFromReversProxy = (
                ($remoteAddress = $this->getServerVar('REMOTE_ADDR')) &&
                in_array($remoteAddress, $proxyServers)
            );

            if ($remoteAddressIsFromReversProxy) {
                if($forwardedIp = $this->getForwardedClientIp()) {
                    return $forwardedIp;
                }

                if (($headerClientIp = $this->Headers()->get('Client-Ip')) &&
                    filter_var($headerClientIp, FILTER_VALIDATE_IP)
                ){
                    return $headerClientIp;
                }
            }

            return $remoteAddress;
        }

        /**
         * Returns the forwarded client ip.
         * @return string the forwarded client ip or null if not available
         */
        public function getForwardedClientIp() {
            $clientIp = null;

            if ($forwardedIps = $this->Headers()->get('X-Forwarded-For')) {
                $forwardedIps = array_filter(
                    preg_split('/[\s]*,[\s]*/', $forwardedIps),
                    function($ip){return filter_var($ip, FILTER_VALIDATE_IP);}
                );

                if (! empty($forwardedIps)) {
                    $clientIp = array_shift($forwardedIps);
                }
            }

            return $clientIp;
        }

        /**
         * Checks if the connection is https.
         * @return boolean check result
         */
        public function isSecureConnection() {
            if ($httpsForwarded = $this->Headers()->get('X-Forwarded-Proto')) {
                return (strtolower($httpsForwarded) == 'https');
            }

            if($secureMode = $this->getServerVar('HTTPS')) {
                return (strtolower($secureMode) != 'off' && $secureMode != '0');
            }

            return false;
        }

        /**
         * Check if the Ajax framework has sent an identifier.
         * This is not standard and is currently just supported by few javascript frameworks.
         * @return boolean check result
         */
        public function isAjaxRequest() {
            return ($this->getServerVar('X-Requested-With') == 'XMLHttpRequest');
        }

        /**
         * Returns the raw input of the request.
         * @return string the raw input
         */
        public function getRawInput() {
            return file_get_contents('php://input');
        }

        /**
         * Returns the http request headers.
         * @retrun string http request headers
         */
        public function toString() {
            $headers  = sprintf("%s %s %s\r\n", $this->getMethod(), $this->Url()->toString(false), $this->getProtocol());
            $headers .= $this->Headers()->toString();

            return $headers;
        }

        /**
         * Supporting casting to string.
         * @return string the http request headers
         */
        public function __toString() {
            return $this->toString();
        }

     }