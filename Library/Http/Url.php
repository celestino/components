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
    use Brickoo\Library\Http\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Url
     *
     * Url class for Uniform Handle Locator specified tasks.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Url implements Interfaces\UrlInterface, \Countable
    {

        /**
         * Holds an instance of the Http Request class.
         * @see Brickoo\Library\Http\Request
         * @var object
         */
        protected $Request;

        /**
         * Lazy initialization of the Http\Request instance.
         * Returns the Http\Request instance.
         * @return object Http\Request implementing the Http\Interfaces\RequestInterface
         */
        public function getRequest()
        {
            if (! $this->Request instanceof Interfaces\RequestInterface)
            {
                $this->injectRequest(new Request());
            }

            return $this->Request;
        }

        /**
         * Injects the Http\Request dependency.
         * @param \Brickoo\Library\Http\Interfaces\RequestInterface $Request the Http\Request instance
         * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependecy
         * @return object reference
         */
        public function injectRequest(\Brickoo\Library\Http\Interfaces\RequestInterface $Request)
        {
            if ($this->Request !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('Http\Interfaces\RequestInterface');
            }

            $this->Request = $Request;

            return $this;
        }

        /**
         * Holds the request scheme (e.g. http/https);
         * @var string
         */
        protected $scheme;

        /**
         * Returns the request scheme.
         * @return string the request scheme
         */
        public function getScheme()
        {
            if ($this->scheme === null)
            {
                $this->scheme = 'http' . ($this->getRequest()->isSecureConnection() ? 's' : '');
            }

            return $this->scheme;
        }

        /**
         * Holds the host name or ip adress of the host.
         * @var string the host name or adress
         */
        protected $hostname;

        /**
         * Returns the host name or ip adress of the host.
         * @return string
         */
        public function getHost()
        {
            $HttpRequest = $this->getRequest();

            if ($this->hostname === null)
            {
                if (! $hostname =  $HttpRequest->getHTTPHeader('Host', false))
                {
                    if (! $hostname = $HttpRequest->getCoreRequest()->getServerVar('Server.Name'))
                    {
                        $hostname = $HttpRequest->getCoreRequest()->getServerVar('Server.Addr');
                    }
                }
                $this->hostname = (string)$hostname;
            }

            return $this->hostname;
        }

        /**
         * Holds the port handling request.
         * @var string
         */
        protected $port;

        /**
         * Returns the port handling request.
         * @return string the server port
         */
        public function getPort()
        {
            $CoreRequest = $this->getRequest()->getCoreRequest();

            if ($this->port === null)
            {
                if (! $port = $CoreRequest->getServerVar('X.Forwarded.Port'))
                {
                    $port = $CoreRequest->getServerVar('Server.Port');
                }
                $this->port = (string)$port;
            }

            return $this->port;
        }

        /**
         * Holds the segments contained in the URL.
         * @var array
         */
        protected $segments;

        /**
         * Returns the available segments containing values.
         * @return array the request URL segments
         */
        public function getSegments()
        {
            if
            (
                empty($this->segments)
                &&
                ($requestPath = $this->getRequestPath())
            )
            {
                $this->segments = explode('/', trim($requestPath, '/'));
            }

            return $this->segments;
        }

        /**
         * Returns the segment value of the passed position.
         * @param integer $position the position of the segment to return
         * @throws OutOfRangeException if the position is out of range
         * @return string the segment value
         */
        public function getSegment($position)
        {
            TypeValidator::IsInteger($position);

            if
            (
                ($segments = $this->getSegments())
                &&
                array_key_exists($position, $segments)
            )
            {
                return rawurldecode($segments[$position]);
            }

            throw new \OutOfRangeException('Segment on position `'. $position .'` is not available.');
        }

        /**
         * Holds the request query string.
         * @var string
         */
        protected $requestQuery;

        /**
         * Returns the request query if available.
         * @return string the request query
         */
        public function getRequestQuery()
        {
            $CoreRequest = $this->getRequest()->getCoreRequest();

            if ($this->requestQuery === null)
            {
                if (! $queryString = $CoreRequest->getServerVar('Query.String'))
                {
                    if (! empty($_GET))
                    {
                        $queryArray = array();
                        foreach ($_GET as $key => $value)
                        {
                            $queryArray[] = $key . '=' . (string)$value;
                        }
                        $queryString = implode('&', $queryArray);
                    }
                    else
                    {
                        $queryString = '';
                    }
                }

                $this->requestQuery = $queryString;
            }

            return $this->requestQuery;
        }

        /**
         * Holds the request path.
         * @var string
         */
        protected $requestPath;

        /**
         * Returns the IIS request path assigned if available.
         * @return string the request path otherwise boolean false
         */
        protected function getIISRequestPath()
        {
            $CoreRequest = $this->getRequest()->getCoreRequest();

            if ($requestPath = $CoreRequest->getServerVar('X.Original.Url'))
            {
                return $requestPath;
            }

            if ($requestPath = $CoreRequest->getServerVar('X.Rewrite.Url'))
            {
                return $requestPath;
            }

            return false;
        }

        /**
         * Returns the request path.
         * @return string the request path
         */
        public function getRequestPath()
        {
            $CoreRequest = $this->getRequest()->getCoreRequest();

            if (! empty($this->requestPath))
            {
                return $this->requestPath;
            }

            if (! $requestPath = $this->getIISRequestPath())
            {
                $requestPath = $CoreRequest->getServerVar('Request.Uri');
            }

            if (! empty($requestPath))
            {
                if (($position = strpos($requestPath, '?')) !== false)
                {
                    $requestPath = substr($requestPath, 0, $position);
                }

                $this->requestPath = '/' . trim($requestPath, '/');
            }

            return $this->requestPath;
        }

        /**
         * Returns the request url.
         * @param boolean $withPort return the url including port
         * @return string the request url
         */
        public function getRequestUrl($withPort = false)
        {
            TypeValidator::IsBoolean($withPort);

            $port = ($withPort ? ':'. $this->getPort() : '');

            $requestQuery = $this->getRequestQuery();

            return $this->getScheme() . '://' . $this->getHost() . $port .
                   $this->getRequestPath() .
                   (empty($requestQuery) ? '' : '?' . $requestQuery);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->reset();
        }

        /**
         * Returns the amount of segments available.
         * @return integer the amount of segments
         */
        public function count()
        {
            return substr_count($this->getRequestPath(), '/');
        }

        /**
         * Clears the object properties.
         * @return object reference
         */
        public function reset()
        {
            $this->scheme        = null;
            $this->hostname      = null;
            $this->requestPath   = null;
            $this->requestQuery  = null;
            $this->segments      = array();

            return $this;
        }

     }

?>