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

    use Brickoo\Library\Core;
    use Brickoo\Library\Http\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Request
     *
     * Request class for accessing Http request content.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class Request implements Interfaces\RequestInterface
    {

        /**
         * Holds an instance of the Core Request class.
         * @see Brickoo\Library\Core\Request
         * @var object
         */
        protected $Request;

        /**
         * Holds an instance of the Url class.
         * @see Bricko\Library\Http\Url
         * @var object
         */
        protected $_Url;

        /**
         * Returns the Url object.
         * If the object does not exist, it wll be created.
         * @return object Url implementing the UrlInterface
         */
        public function Url()
        {
            if (! $this->_Url instanceof Interfaces\UrlInterface)
            {
                $this->setUrlSupport(new Url($this->Request));
            }

            return $this->_Url;
        }

        /**
         * Lazy initialization of the Url instance.
         * @param UrlInterface $Url the Url objetc implementing the UrlInterface
         * @return object reference
         */
        public function setUrlSupport(\Brickoo\Library\Http\Interfaces\UrlInterface $Url)
        {
            if ($this->_Url !== null)
            {
                throw new Core\Exceptions\DependencyOverrideException('Http\Interfaces\UrlInterface');
            }

            $this->_Url = $Url;

            return $this;
        }

    	/**
         * Holds the request variables order to collect.
         * @var string
         */
        protected $variablesOrder;

        /**
         * Returns the current request variables order.
         * @return array containing the variables order
         */
        public function getVariablesOrder()
        {
            return $this->variablesOrder;
        }

        /**
         * Sets the request variables order to use.
         * @param string $order the collecting order
         * @return object reference
         */
        public function setVariablesOrder($order)
        {
            TypeValidator::Validate('isString', array($order));
            TypeValidator::Validate('useRegex', array(array('~^[GPCF]{1,3}$~i', $order)));

            $this->variablesOrder = $this->filterOrderChars($order);
            $this->params = null;

            return $this;
        }

        /**
         * Filter the variables order to have unique order chars.
         * @param string $order the varaibles order
         * @return string the filtered variables order
         */
        protected function filterOrderChars($order)
        {
            TypeValidator::Validate('isString', array($order));

            $filterChars = array();
            $orderLength = strlen($order);

            for ($position=0; $position < $orderLength; ++$position)
            {
                $char = strtoupper(substr($order, $position, 1));
                if (in_array($char, $filterChars))
                {
                    continue;
                }
                $filterChars[] = $char;
            }

            return $filterChars;
        }

        /**
         * Holds the request parameters.
         * @var array
         */
        protected $params;

        /**
         * Returns all available request parameters like the superglobal
         * $_REQUEST variable does with the order previously set.
         * The Parameters are returned as an associative array.
         * @return array the request parameters
         */
        public function getParams()
        {
            if (empty($this->params))
            {
                $this->collectParams();
            }

            return $this->params;
        }

        /**
         * Returns the value of the given request parameter.
         * @param string $paramName the parameter name
         * @param mixed $defaultValue the default value to return
         * @return string the request parameter value or the default value
         */
        public function getParam($paramName, $defaultValue = null)
        {
            TypeValidator::Validate('isString', array($paramName));

            if (empty($this->params))
            {
                $this->collectParams();
            }

            if (! array_key_exists($paramName, $this->params))
            {
                return $defaultValue;
            }

            return $this->params[$paramName];
        }

        /**
         * Collects the parameters in the given order.
         * @return void
         */
        protected function collectParams()
        {
            if (! empty($this->variablesOrder))
            {
                foreach($this->variablesOrder as $char)
                {
                    switch ($char)
                    {
                        case 'G':
                            $this->params = array_replace($this->params, $_GET);
                            break;
                        case 'P':
                            $this->params = array_replace($this->params, $_POST);
                            break;
                        case 'C':
                            $this->params = array_replace($this->params, $_COOKIE);
                            break;
                        case 'F':
                            $this->params = array_replace($this->params, $_FILES);
                            break;
                    }
                };
            }
        }

        /**
         * Holds the request available http headers.
         * @var array
         */
        protected $HTTPheaders;

        /**
         * Filters the headers by the keys used.
         * @param array $headers the key-value pairs to add
         * @param boolean $override flag to force overriding
         * @return array the cleaned headers to add.
         */
        protected function filterHeaders(array $headers, $override = false)
        {
            TypeValidator::Validate('isArray', array($headers));
            TypeValidator::Validate('isBoolean', array($override));

            $headersModified = array();

            foreach ($headers as $headerKey => $headerValue)
            {
                if
                (
                    empty($headerKey) ||
                    (! is_string($headerKey)) ||
                    (
                        (! $override)
                        &&
                        $this->isHTTPHeaderAvailable($headerKey)
                    )
                )
                {
                    continue;
                }

                $headerKey = strtoupper(trim($headerKey));

                $headersModified[$headerKey] = $headerValue;
            }

            return $headersModified;
        }

        /**
         * Add headers to the current available http headers.
         * If the override flag is true the headers will be overriden.
         * @param array $headers the key-value pairs to add
         * @param boolean $override flag to force overriding
         * @return object reference
         */
        public function addHTTPHeaders(array $headers, $override = false)
        {
            TypeValidator::Validate('isArray', array($headers));
            TypeValidator::Validate('isBoolean', array($override));

            $headers = $this->filterHeaders($headers, $override);

            if (! empty($headers))
            {
                $this->HTTPheaders = array_replace($this->HTTPheaders, $headers);
            }

            return $this;
        }

        /**
         * Returns all available http headers.
         * @return array the available http headers
         */
        public function getHTTPHeaders()
        {
            if (empty($this->HTTPheaders))
            {
                $this->collectHTTPHeaders();
            }

            return $this->HTTPheaders;
        }

        /**
         * Return the value of the given http header if available.
         * @param string $headerName the header name to retrieve the value from
         * @param mixed $defaultValue the default value to return
         * @return string/mixed the header or the default value
         */
        public function getHTTPHeader($headerName, $defaultValue = null)
        {
            TypeValidator::Validate('isString', array($headerName));

            $headerName = strtoupper(str_replace(array('-', ' ', '.'), '_', $headerName));

            if (empty($this->HTTPheaders))
            {
                $this->collectHTTPHeaders();
            }

            if (! array_key_exists($headerName, $this->HTTPheaders))
            {
                return $defaultValue;
            }

            return $this->HTTPheaders[$headerName];
        }

        /**
         * Check if the given http header name is available.
         * @param string $headerName the http header name to check
         * @return boolean check result
         */
        public function isHTTPHeaderAvailable($headerName)
        {
            TypeValidator::Validate('isString', array($headerName));

            $headerName = strtoupper(str_replace(array('-', ' ', '.'), '_', $headerName));

            if (empty($this->HTTPheaders))
            {
                $this->collectHTTPHeaders();
            }

            return array_key_exists($headerName, $this->HTTPheaders);
        }

        /**
         * Collects the http headers.
         * Adds the HTTP_*** headers to the local container.
         * @return void
         */
        protected function collectHTTPHeaders()
        {
            if
            (
                isset($_SERVER)
                &&
                (! empty($_SERVER))
            )
            {
                foreach ($_SERVER as $key => $value)
                {
                    if (substr($key, 0, 5) == 'HTTP_')
                    {
                        $key = strtoupper(substr($key, 5));
                        $this->HTTPheaders[$key] = $value;
                    }
                }
            }

            if ($apacheHeaders = $this->collectApacheHeaders())
            {
                $this->HTTPheaders = array_merge($this->HTTPheaders, $apacheHeaders);
            }
        }

        /**
         * Returns the available apache headers.
         * @return array the apache headers available
         */
        protected function collectApacheHeaders()
        {
           $apacheHeaders = array();

            if
            (
                function_exists('apache_request_headers')
                &&
                ($headers = apache_request_headers())
            )
            {
                foreach($headers as $key => $value)
                {
                    $key = strtoupper(str_replace(array('-', ' ', '.'), '_', $key));
                    $apacheHeaders[$key] = $value;
                }
            }

            return $apacheHeaders;
        }

        /**
         * Holds the accept types supported.
         * @var array
         */
        protected $acceptTypes;

        /**
         * Returns the accept types supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
         * @param string $neededType the type which is needed if supported
         * @return array accept types sorted by priority descening
         * or the supported types otherwise
         */
        public function getAcceptTypes()
        {
            if
            (
                empty($this->acceptTypes)
                &&
                ($acceptHeader = $this->getHTTPHeader('Accept'))
            )
            {
                $this->acceptTypes = $this->getAcceptHeaderByRegex
                (
                    '~^(?<type>[a-z\/\+\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?~i',
                    'type',
                    $acceptHeader
                );
            }

            return $this->acceptTypes;
        }

        /**
         * Checks if the passed type is supported.
         * @param string $acceptType the accept type to check
         * @return boolean check result
         */
        public function isTypeSupported($acceptType)
        {
            TypeValidator::Validate('isString', array($acceptType));

            return array_key_exists(strtolower($acceptType), $this->getAcceptTypes());
        }

        /**
         * Holds the accept languages supported.
         * @var array
         */
        protected $acceptLanguages;

        /**
         * Returns the accept languages supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
         * @return array the languages sorted by priority descening
         */
        public function getAcceptLanguages()
        {
            if
            (
                empty($this->acceptLanguages)
                &&
                ($acceptLanguageHeader = $this->getHTTPHeader('Accept.Language'))
            )
            {
                $this->acceptLanguages = $this->getAcceptHeaderByRegex
                (
                    '~^(?<language>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'language',
                    $acceptLanguageHeader
                );
            }

            return $this->acceptLanguages;
        }

        /**
         * Checks if the passed language is supported.
         * @param string $acceptLanguage the accept language to check
         * @return boolean check result
         */
        public function isLanguageSupported($acceptLanguage)
        {
            TypeValidator::Validate('isString', array($acceptLanguage));

            return array_key_exists($acceptLanguage, $this->getAcceptLanguages());
        }

        /**
         * Holds the accept encodings supported.
         * @var array
         */
        protected $acceptEncodings;

        /**
         * Returns the accept encodings supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
         * @return array the encondings sorted by priority descening
         */
        public function getAcceptEncodings()
        {
            if
            (
                empty($this->acceptEncodings)
                &&
                ($acceptEncodingHeader = $this->getHTTPHeader('Accept.Encoding'))
            )
            {
                $this->acceptEncodings = $this->getAcceptHeaderByRegex
                (
                    '~^(?<encoding>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'encoding',
                    $acceptEncodingHeader
                );
            }

            return $this->acceptEncodings;
        }

        /**
         * Checks if the passed encoding type is supported.
         * @param string $acceptEncoding the accept encoding type to check
         * @return boolean check result
         */
        public function isEncodingSupported($acceptEncoding)
        {
            TypeValidator::Validate('isString', array($acceptEncoding));

            return array_key_exists($acceptEncoding, $this->getAcceptEncodings());
        }

        /**
         * Holds the accept charsets supported.
         * @var array
         */
        protected $acceptCharsets;

        /**
         * Returns the accept charsets supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2
         * @return array the charsets sorted by priority descening
         */
        public function getAcceptCharsets()
        {
            if
            (
                empty($this->acceptCharsets)
                &&
                ($acceptEncodingHeader = $this->getHTTPHeader('Accept.Charset'))
            )
            {
                $this->acceptCharsets = $this->getAcceptHeaderByRegex
                (
                    '~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'charset',
                    $acceptEncodingHeader
                );
            }

            return $this->acceptCharsets;
        }

        /**
         * Checks if the passed encoding type is supported.
         * @param string $acceptEncoding the accept encoding type to check
         * @return boolean check result
         */
        public function isCharsetSupported($acceptCharset)
        {
            TypeValidator::Validate('isString', array($acceptCharset));

            return array_key_exists($acceptCharset, $this->getAcceptCharsets());
        }

        /**
         * Returns the accept header value sorted by quality.
         * @param string $regex the regular expression to use
         * @param string $keyName the key name to assign the quality to
         * @param string $acceptHeader the accept header to retireve the values from
         * @return array the result containing the header values
         */
        public function getAcceptHeaderByRegex($regex, $keyName, $acceptHeader)
        {
            TypeValidator::Validate('isString', array($regex, $keyName, $acceptHeader));

            $results = array();
            $fields = explode(',', $acceptHeader);

            foreach ($fields as $field)
            {
                if (preg_match($regex, trim($field), $matches))
                {
                    if (isset($matches[$keyName]))
                    {
                        $matches['quality'] = (isset($matches['quality']) ?: 1.0);
                        $results[trim($matches[$keyName])] = (float)$matches['quality'];
                    }
                }
            }

            arsort($results);
            return $results;
        }

        /**
         * Returns the request method.
         * @return strinng the request value or null if not given.
         */
        public function getRequestMethod()
        {
            return $this->Request->getServerVar('request.method');
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
            if ($httpsForwarded = $this->Request->getServerVar('X.Forwarded.Proto'))
            {
                return (strtolower($httpsForwarded) == 'https');
            }

            $secureMode = $this->Request->getServerVar('https', $this->Request->getServerVar('ssl.https'));

            return (
                strtolower($secureMode) == 'on' ||
                (
                    strtolower($secureMode) != 'off'
                    &&
                    (! empty($secureMode))
                )
            );
        }

        /**
         * Check if the Ajax framework has sent an identifier.
         * This is not standard and is currently just supported by few javascript frameworks.
         * @return boolean check result
         */
        public function isAjaxRequest()
        {
            return ($this->Request->getServerVar('X.Requested.With') == 'XMLHttpRequest');
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param object implementing the Brickoo\Library\Core\Interfaces\RequestInterface
         * @return void
         */
        public function __construct(\Brickoo\Library\Core\Interfaces\RequestInterface $Request)
        {
            $this->Request = $Request;
            $this->clear();
        }

        /**
         * Clears the object properties.
         * @return object reference
         */
        public function clear()
        {
            $this->_Url               = null;
            $this->serverVars         = array();
            $this->variablesOrder     = array('G', 'P', 'C', 'F');
            $this->HTTPheaders        = array();
            $this->params             = array();
            $this->acceptTypes        = array();
            $this->acceptLanguages    = array();
            $this->acceptEncodings    = array();
            $this->acceptCharsets     = array();

            return $this;
        }

     }

?>