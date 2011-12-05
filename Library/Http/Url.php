<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    use Brickoo\Library\Core\Interfaces\RequestInterface;
    use Brickoo\Library\Http\Interfaces\UrlInterface;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Url
     *
     * Url class for Uniform Resoruce Locator specified tasks.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */

    class Url implements UrlInterface, \Countable
    {

        /**
         * Holds an instance of the Core Request class.
         * @see Brickoo\Library\Core\Request
         * @var object
         */
        protected $Request;

        /**
         * Holds the request scheme (e.g. http/https);
         * @var string
         */
        protected $scheme;

        /**
         * Returns the request scheme.
         * @return string
         */
        public function getScheme()
        {
            if ($this->scheme === null)
            {
                $this->scheme = 'http' . ($this->Request->Http()->isSecureConnection() ? 's' : '');
            }

            return $this->scheme;
        }

        /**
         * Holds the host name or ip adress of the host.
         * @var string
         */
        protected $hostname;

        /**
         * Returns the host name or ip adress of the host.
         * @return string
         */
        public function getHost()
        {
            if ($this->hostname === null)
            {
                if (! $hostname =  $this->Request->Http()->getHTTPHeader('Host', false))
                {
                    if (! $hostname = $this->Request->getServerVar('Server.Name'))
                    {
                        $hostname = $this->Request->getServerVar('Server.Addr');
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
         * @return string
         */
        public function getPort()
        {
            if ($this->port === null)
            {
                if (! $port = $this->Request->getServerVar('X.Forwarded.Port'))
                {
                    $port = $this->Request->getServerVar('Server.Port');
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
         * Returns the available segments.
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
                $this->segments = explode('/', $requestPath);
            }

            return $this->segments;
        }

        /**
         * Returns the segment value of the passed position.
         * @param integer $position the position of the segment to return
         * @throws OutOfRangeException if the index is out of range
         * @return string the segment value
         */
        public function getSegment($position)
        {
            TypeValidator::Validate('isInteger', array($position));

            if
            (
                ($segments = $this->getSegments())
                &&
                array_key_exists($position, $segments)
            )
            {
                return rawurldecode($segments[$position]);
            }

            throw new \OutOfRangeException('Segment on position `'. $position .'` is not available.', E_WARNING);
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
            if ($this->requestQuery === null)
            {
                if (! $queryString = $this->Request->getServerVar('Query.String'))
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
            if ($requestPath = $this->Request->getServerVar('X.Original.Url'))
            {
                return $requestPath;
            }

            if ($requestPath = $this->Request->getServerVar('X.Rewrite.Url'))
            {
                return $requestPath;
            }

            return false;
        }

        /**
         * Returns the request path.
         * @return string path
         */
        public function getRequestPath()
        {
            if (! empty($this->requestPath))
            {
                return $this->requestPath;
            }

            if (! $requestPath = $this->getIISRequestPath())
            {
                $requestPath = $this->Request->getServerVar('Request.Uri');
            }

            if (! empty($requestPath))
            {
                if (($position = strpos($requestPath, '?')) !== false)
                {
                    $requestPath = substr($requestPath, 0, $position);
                }

                $this->requestPath = trim($requestPath, '/');
            }

            return $this->requestPath;
        }

        /**
         * Returns the request url.
         * @param boolean $withPort return the url including port
         * @return string
         */
        public function getRequestUrl($withPort = false)
        {
            TypeValidator::Validate('isBoolean', array($withPort));

            $port = ($withPort ? ':'. $this->getPort() : '');

            $requestQuery = $this->getRequestQuery();

            return $this->getScheme() . '://' . $this->getHost() . $port .
                   '/' . $this->getRequestPath() .
                   (empty($requestQuery) ? '' : '?' . $requestQuery);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param object Request object implementing the RequestInterface
         * @return void
         */
        public function __construct(RequestInterface $Request)
        {
            $this->Request = $Request;
            $this->clear();
        }

        /**
         * Returns the amount of segments available.
         * @return integer the amount of segments
         */
        public function count()
        {
            $segments = $this->getSegments();
            return count($segments);
        }

        /**
         * Clears the object properties.
         * @return object reference
         */
        public function clear()
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