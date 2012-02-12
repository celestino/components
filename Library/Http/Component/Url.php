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

    namespace Brickoo\Library\Http\Component;

    use Brickoo\Library\Core;
    use Brickoo\Library\Http;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Url
     *
     * Url class for Uniform Handle Locator specified tasks.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Url implements Interfaces\UrlInterface
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
         * @param object $Dependecy the dependecy used to overwrite
         * @return object Url if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependecy = null)
        {
            if ($Dependecy instanceof $interface) {
                $this->dependencies[$name] = $Dependecy;
                return $this;
            }
            elseif (! $this->dependencies[$name] instanceof $interface) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the Http\Request instance.
         * @param \Brickoo\Library\Http\Interfaces\RequestInterface $Request the Http\Request instance
         * @return \Brickoo\Library\Http\Interfaces\RequestInterface
         */
        public function Request(\Brickoo\Library\Http\Interfaces\RequestInterface $Request = null)
        {
            return $this->getDependency(
                'Request',
                '\Brickoo\Library\Http\Interfaces\RequestInterface',
                function(){return new Http\Request();},
                $Request
            );
        }

        /**
         * Holds the url scheme (e.g. http/https);
         * @var string
         */
        protected $scheme;

        /**
         * Returns the request scheme.
         * @throws \UnexpectedValueException if the the scheme is not set
         * @return string the url scheme
         */
        public function getScheme()
        {
            if ($this->scheme === null) {
                throw new \UnexpectedValueException('The scheme is not set.');
            }

            return $this->scheme;
        }

        /**
         * Sets the url scheme.
         * @param string $scheme the url scheme to set
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function setScheme($scheme)
        {
            TypeValidator::IsString($scheme);

            $this->scheme = $scheme;

            return $this;
        }

        /**
         * Holds the host name or ip adress of the host.
         * @var string the host name or adress
         */
        protected $host;

        /**
         * Returns the host name or ip adress of the host.
         * @throws \UnexpectedValueException if the host is not set
         * @return string
         */
        public function getHost()
        {
            if ($this->host === null) {
                throw new \UnexpectedValueException('The host is not set.');
            }

            return $this->host;
        }

        /**
         * Sets the host.
         * @param string $host the host to set
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function setHost($host)
        {
            TypeValidator::IsString($host);

            $this->host = $host;

            return $this;
        }

        /**
         * Holds the port handling request.
         * @var string
         */
        protected $port;

        /**
         * Returns the port handling request.
         * @return string the url included port or null if not set
         */
        public function getPort()
        {
            return $this->port;
        }

        /**
         * Sets the url included port.
         * @param string|integer $port the url port
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function setPort($port)
        {
            TypeValidator::IsStringOrInteger($port);

            $this->port = (int)$port;

            return $this;
        }

        /**
         * Holds the url query string.
         * @var string
         */
        protected $query;

        /**
         * Returns the url query string.
         * @return string the url query string or null if not set
         */
        public function getQuery()
        {
            return $this->query;
        }

        /**
         * Sets the url query string.
         * @param string $query the query string to set
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function setQuery($query)
        {
            TypeValidator::IsString($query);

            $this->query = $query;

            return $this;
        }

        /**
         * Holds the url path.
         * @var string
         */
        protected $path;

        /**
         * Returns the url path.
         * @throws \UnexpectedException if the path is not set
         * @return string the url path
         */
        public function getPath()
        {
            if ($this->path === null) {
                throw new \UnexpectedValueException('The path is not set.');
            }

            return $this->path;
        }

        /**
         * Sets the url path.
         * @param string $path the url path to set
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function setPath($path)
        {
            TypeValidator::IsString($path);

            $this->path = '/' . trim($path, '/');

            return $this;
        }

        /**
         * Holds the url format.
         * @var string
         */
        protected $format;

        /**
         * Returns the url format.
         * @return string the request format or null if not set
         */
        public function getFormat()
        {
            return $this->format;
        }

        /**
         * Sets the url format.
         * @param string $format the request format
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function setFormat($format)
        {
            TypeValidator::IsString($format);

            $this->format = $format;

            return $this;
        }

        /**
         * Imports the url parts from string.
         * @param string $url the url to import from
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function importFromString($url)
        {
            TypeValidator::IsString($url);

            if (! preg_match('~^[^:/?#]+://[^/?#]+(\?[^#]*)?(#.*)?~', $url)) {
                throw new \InvalidArgumentException(sprintf('The url `%s` does not match a valid URL', $url));
            }

            preg_match(
                '~^((?<setScheme>[^:/?#]+):(//))?'.
                '('.
                    '(\3|//)?'.
                    '(?:(?<user>[^:]+):(?<pass>[^@]+)@)?'.
                    '(?<setHost>[^/?:#]*))(:(?<setPort>\d+)'.
                ')?'.
                '(?<setPath>[^\.?#]*)?'.
                '(\.(?<setFormat>[^?#]*))?'.
                '(\?(?<setQuery>[^#]*))?'.
                '(#(?<fragment>.*))?~u',
                $url,
                $urlParts
            );

            foreach(array('setScheme', 'setHost', 'setPort', 'setPath', 'setFormat', 'setQuery') as $key) {
                if (! empty($urlParts[$key])) {
                    $this->$key($urlParts[$key]);
                }
            }

            return $this;
        }

        /**
         * Imports the request configuration.
         * @return \Brickoo\Library\Http\Component\Url
         */
        public function importFromGlobals()
        {
            $this->scheme      = 'http' . ($this->Request()->isSecureConnection() ? 's' : '');
            $this->host        = $this->getRequestHost();
            $this->port        = $this->getRequestPort();
            $this->query       = $this->getRequestQuery();
            $this->path        = $this->getRequestPath();
            $this->format      = $this->getRequestFormat();

            return $this;
        }

        /**
         * Returns the request available host name or adress.
         * @return string the request host
         */
        public function getRequestHost()
        {
            if ((! $host =  $this->Request()->Headers()->get('Host')) && (! $host = $this->Request()->getServerVar('SERVER_NAME'))) {
                $host = $this->Request()->getServerVar('SERVER_ADDR');
            }

            return $host;
        }

        /**
         * Returns the request request port.
         * @return integer the request port
         */
        public function getRequestPort()
        {
            if (! $port = $this->Request()->Headers()->get('X-Forwarded-Port')) {
                $port = $this->Request()->getServerVar('SERVER_PORT');
            }

            return (int)$port;
        }

        /**
         * Returns the request request query string or one build from the $_GET paramater.
         * @return the request query string
         */
        public function getRequestQuery()
        {
            if (! $queryString = $this->Request()->getServerVar('QUERY_STRING')) {
                $queryArray = array();
                foreach ($_GET as $key => $value) {
                    $queryArray[] = $key . '=' . (string)$value;
                }
                $queryString = implode('&', $queryArray);
            }

            return $queryString;
        }

        /**
         * Returns the request path.
         * @return string the request request path
         */
        public function getRequestPath()
        {
            if (! $requestPath = $this->getIISRequestUrl()) {
                $requestPath = $this->Request()->getServerVar('REQUEST_URI');
            }

            if (($position = strpos($requestPath, '?')) !== false) {
                $requestPath = substr($requestPath, 0, $position);
            }

            return '/' . trim($requestPath, '/');
        }

        /**
         * Returns the request format.
         * @return string the request format
         */
        public function getRequestFormat()
        {
            $requestFormat = '';

            if (($path = $this->getPath()) && ($position = strpos($path, '.'))) {
                $requestFormat = substr($path, $position + 1);
            }

            return $requestFormat;
        }

        /**
         * Returns the IIS request url assigned if available.
         * @return string the request url
         */
        public function getIISRequestUrl()
        {
            if (! $requestPath = $this->Request()->Headers()->get('X-Original-Url')) {
                $requestPath = $this->Request()->Headers()->get('X-Rewrite-Url');
            }

            return $requestPath;
        }

        /**
         * Returns the full url.
         * @param boolean $withHost flag to include the host
         * @return string the full url
         */
        public function toString($withHost = true)
        {
            TypeValidator::IsBoolean($withHost);

            $host = '';
            if ($withHost) {
                $host = sprintf('%s://%s', $this->getScheme(), $this->getHost());
                if ($this->getPort() != 80 && $this->getPort() !=443) {
                    $host .= sprintf(':%s', $this->getPort());
                }
            }

            return  $host . $this->getPath() . (($query = $this->getQuery()) ? sprintf('?%s', $query) : '');
        }

        /**
         * Supporting casting to string.
         * @return string the collected headers
         */
        public function __toString()
        {
            return $this->toString();
        }

     }