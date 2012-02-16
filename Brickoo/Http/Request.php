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

    namespace Brickoo\Library\Http;

    use Brickoo\Library\Core;
    use Brickoo\Library\Memory;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Request
     *
     * Implements methods to create or handle a http request.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Request implements Interfaces\RequestInterface, Core\Interfaces\RequestInterface
    {

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
         * @param object $Dependecy the dependecy to inject
         * @return object Request if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependecy = null)
        {
            if ($Dependecy instanceof $interface) {
                $this->dependencies[$name] = $Dependecy;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the Url dependency
         * @param \Brickoo\Library\Http\Component\Interfaces\UrlInterface $Url the Url dependency to inject
         * @return \Brickoo\Library\Http\Interfaces\UrlInterface
         */
        public function Url(\Brickoo\Library\Http\Component\Interfaces\UrlInterface $Url = null)
        {
            return $this->getDependency(
                'Url',
                '\Brickoo\Library\Http\Component\Interfaces\UrlInterface',
                function($Request) {
                    $Url = new Component\Url();
                    $Url->Request($Request);
                    return $Url->importFromGlobals();
                },
                $Url
            );
        }

        /**
         * Lazy initialization of the Headers dependency.
         * @param \Brickoo\Library\Http\Component\Interfaces\HeadersInterface $Headers the Headers dependency to inject
         * @return \Brickoo\Library\Http\Component\Interfaces\HeadersInterface
         */
        public function Headers(\Brickoo\Library\Http\Component\Interfaces\HeadersInterface $Headers = null)
        {
            return $this->getDependency(
                'Headers',
                '\Brickoo\Library\Http\Component\Interfaces\HeadersInterface',
                function() {
                    $Headers = new Component\Headers();
                    return $Headers->importFromGlobals();
                },
                $Headers
            );
        }

        /**
         * Lazy initialization of the Query dependency.
         * @param \Brickoo\Library\Http\Component\Interfaces\QueryInterface $Query the Query dependency to inject
         * @return \Brickoo\Library\Http\Component\Interfaces\QueryInterface
        */
        public function Query(\Brickoo\Library\Http\Component\Interfaces\QueryInterface $Query = null)
        {
            return $this->getDependency(
                'Query',
                '\Brickoo\Library\Http\Component\Interfaces\QueryInterface',
                function() {
                    $Query = new Component\Query();
                    return $Query->importFromGlobals();
                },
                $Query
            );
        }

        /**
         * Lazy initialization of the Post dependency.
         * @param \Brickoo\Library\Memory\Interfaces\ContainerInterface $Post the Post dependency to inject
         * @return \Brickoo\Library\Memory\Interfaces\ContainerInterface
         */
        public function Post(\Brickoo\Library\Memory\Interfaces\ContainerInterface $Post = null)
        {
            return $this->getDependency(
                'Post',
                '\Brickoo\Library\Memory\Interfaces\ContainerInterface',
                function(){
                    $Container = new Memory\Container();
                    $Container->merge($_POST);
                    return $Container;
                },
                $Post
            );
        }

        /**
         * Lazy initialization of the Files dependency.
         * @param \Brickoo\Library\Memory\Interfaces\ContainerInterface $Files the Files dependency to inject
         * @return \Brickoo\Library\Memory\Interfaces\ContainerInterface
         */
        public function Files(\Brickoo\Library\Memory\Interfaces\ContainerInterface $Files = null)
        {
            return $this->getDependency(
                'Files',
                '\Brickoo\Library\Memory\Interfaces\ContainerInterface',
                function(){
                    $Container = new Memory\Container();
                    $Container->merge($_FILES);
                    return $Container;
                },
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
        public function getProtocol()
        {
            if ($this->protocol === null) {
                $this->protocol = $this->getServerVar('SERVER_PROTOCOL');
            }

            return $this->protocol;
        }

        /**
         * Sets the request protocol used.
         * @param string $protocol the protocol to set.
         * @return \Brickoo\Library\Http\Request
         */
        public function setProtocol($protocol)
        {
            TypeValidator::IsString($protocol);
            TypeValidator::MatchesRegex('~^HTTP/1\.[1|0]$~', $protocol);

            $this->protocol = $protocol;

            return $this;
        }

        /**
         * Holds the supported http methods.
         * @var array
         */
        protected $supportedMethods;

        /**
         * Returns the supported methods.
         * @return array containing the supported methods
         */
        public function getSupportedMethods()
        {
            return $this->supportedMethods;
        }

        /**
         * Sets the supported http methods.
         * @param array $methods the methods supported
         * @return \Brickoo\Library\Http\Request
         */
        public function setSupportedMethods(array $methods)
        {
            $this->supportedMethods = $methods;

            return $this;
        }

        /**
         * Checks if the http method is supported.
         * @param string $method the http method to heck
         * @return boolean check result
         */
        public function isSupportedMethod($method)
        {
            TypeValidator::IsString($method);

            return in_array(strtoupper($method), $this->supportedMethods);
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
        public function getMethod()
        {
            if ($this->method === null) {
                $method = $this->getServerVar('REQUEST_METHOD', 'GET');
                $this->setMethod(($this->isSupportedMethod($method) ? strtoupper($method) : 'GET'));
            }

            return $this->method;
        }

        /**
         * Sets the http request method.
         * @param string $method the http request method
         * @throws Exceptions\MethodNotSupported if the method is not supported
         * @return \Brickoo\Library\Http\Request
         */
        public function setMethod($method)
        {
            TypeValidator::IsString($method);

            if (! $this->isSupportedMethod($method)) {
                throw new Exceptions\MethodNotSupportedException($method);
            }

            $this->method = strtoupper($method);

            return $this;
        }

        /**
         * Returns the server hostname.
         * @return the server hostname
         */
        public function getHost()
        {
            return $this->Url()->getHost();
        }

        /**
         * Sets the host name.
         * @param string $host the host name to set
         * @return \Brickoo\Library\Http\Request
         */
        public function setHost($host)
        {
            TypeValidator::IsString($host);
            $this->Url()->setHost($host);

            return $this;
        }

        /**
         * Returns the request path.
         * @return string the request path
         */
        public function getPath()
        {
            return $this->Url()->getPath();
        }

        /**
         * Sets the request path.
         * @param string $path the request path to set
         * @return \Brickoo\Library\Http\Request
         */
        public function setPath($path)
        {
            TypeValidator::IsString($path);
            $this->Url()->setPath($path);

            return $this;
        }

        /**
         * Returns the request format.
         * @return string the request format or null if not set
         */
        public function getFormat()
        {
            return $this->Url()->getFormat();
        }

        /**
         * Sets the request format.
         * @param string $format the request format
         * @return \Brickoo\Library\Http\Request
         */
        public function setFormat($format)
        {
            TypeValidator::IsString($format);
            $this->Url()->setFormat($format);

            return $this;
        }

        /**
         * Class constructor.
         * Initializes class properties.
         * @return void
         */
        public function __construct()
        {
            $this->method              = null;
            $this->supportedMethods    = array('HEAD', 'GET', 'POST', 'PUT', 'DELETE');
            $this->dependencies        = array();
        }

        /**
         * Returns the server variable value if available.
         * @param string $name the server variable name to retrieve the value from
         * @param mixed $defaultValue the default value to return
         * @return string the value of the server variable otherwise mixed the default value
         */
        public function getServerVar($name, $defaultValue = null)
        {
            TypeValidator::IsString($name);

            if (isset($_SERVER[$name])) {
                return $_SERVER[$name];
            }

            return $defaultValue;
        }

        /**
         * Returns the raw body of the request.
         * @return string
         */
        public function getRawBody()
        {
            return file_get_contents('php://input');
        }

        /**
         * Checks if the connection is based on https.
         * @return boolean check result
         */
        public function isSecureConnection()
        {
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
        public function isAjaxRequest()
        {
            return ($this->getServerVar('X-Requested-With') == 'XMLHttpRequest');
        }

        /**
         * Returns the http request headers.
         * @retrun string http request headers
         */
        public function toString()
        {
            $headers  = sprintf("%s %s %s\r\n", $this->getMethod(), $this->Url()->toString(false), $this->getProtocol());
            $headers .= $this->Headers()->toString();

            return $headers;
        }

        /**
         * Supporting casting to string.
         * @return string the http request headers
         */
        public function __toString()
        {
            return $this->toString();
        }

     }