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

    namespace Brickoo\Library\Cli;

    use Brickoo\Library\Core;
    use Brickoo\Library\Cli\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Request
     *
     * Request class for handling a cli request.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Request implements Interfaces\RequestInterface, Core\Interfaces\DynamicRequestInterface
    {

        /**
         * Holds the passed cli arguments.
         * @var array
         */
        protected $arguments;

        /**
         * Returns the available cli arguments.
         * @return array the avialable cli arguments
         */
        public function getArguments()
        {
            if (empty($this->arguments)) {
                $this->collectArguments();
            }

            return $this->arguments;
        }

        /**
         * Returns the passed arguement index value.
         * @param string|integer $index the index of the arguments to retrieve
         * @param mixed $defaultValue the default value if the index does not exist
         * @throws InvalidArgumentException if the index is not valid
         * @return string the argument value or the mixed default value
         */
        public function getArgument($index, $defaultValue = null)
        {
            TypeValidator::IsStringOrInteger($index);

            if (empty($this->arguments)) {
                $this->collectArguments();
            }

            if (array_key_exists($index, $this->arguments)) {
                return $this->arguments[$index];
            }

            return $defaultValue;
        }

        /**
         * Sets the array values as cli arguments keys.
         * The order ist assigned as the passed array is given.
         * If the arguments is not set, null will be assigned
         * @param array $keys the keys to assign arguments to by order
         * @throws InvalidArgumentException if the index is not valid
         * @return \Brickoo\Library\Cli\Request
         */
        public function setArgumentsKeys(array $keys)
        {
            $keys = array_values($keys);

            if (empty($this->arguments)) {
                $this->collectArguments();
            }

            foreach($keys as $index => $value) {
                if(array_key_exists($index, $this->arguments)) {
                    $this->arguments[$value] = $this->arguments[$index];
                }
                else {
                    $this->arguments[$value] = null;
                }
            }

            return $this;
        }

        /**
         * Collects the available request arguments.
         * @return void
         */
        protected function collectArguments()
        {
            if(($arguments = $this->getServerVar('argv')) && is_array($arguments)) {
                $this->arguments = $arguments;
            }
        }

        /**
         * Checks if the request has passed cli arguments.
         * @return boolean result
         */
        public function hasArguments()
        {
            $arguments = $this->getArguments();
            return (! empty($arguments));
        }

        /**
         * Returns the number of the request passed cli arguments.
         * @return integer the number of arguments passed
         */
        public function countArguments()
        {
            $arguments = $this->getArguments();
            return count($arguments);
        }

        public function getProtocol()
        {
            //
        }

        /**
         * Holds the request path assigned.
         * @var string
         */
        protected $path;

        /**
         * Returns the request path used.
         * Needs to be implemented by the DynamicRequestInterface.
         * @throws UnexpectedValueException if the request path is not set
         * @return string the request path
         */
        public function getPath()
        {
            if ($this->path === null) {
                throw new \UnexpectedValueException('The request path is `null`.');
            }

            return $this->path;
        }

        /**
         * Sets the request path to use.
         * Needs to be implemented by the DynamicRequestInterface.
         * @param string $requestPath the request path to use
         * @return \Brickoo\Library\Cli\Request
         */
        public function setPath($requestPath)
        {
            TypeValidator::IsString($requestPath);

            $this->path = rtrim($requestPath, '/');

            return $this;
        }

        /**
         * Holds the request method..
         * @var string
         */
        protected $method;

        /**
         * Returns the request method.
         * This method should always return LOCAL for compatibility with other modules.
         * Needs to be implemented by the DynamicRequestInterface.
         * @return string the request method
         */
        public function getMethod()
        {
            if ($this->method === null) {
                $this->method =  'LOCAL';
            }

            return $this->method;
        }

        /**
         * Sets the request method.
         * Needs to be implemented by the DynamicRequestInterface.
         * @param string $method the request method to set
         * @return \Brickoo\Library\Cli\Request
         */
        public function setMethod($method)
        {
            TypeValidator::IsString($method);

            $this->method = strtoupper($method);

            return $this;
        }

        /**
         * Holds the host name.
         * @var string
         */
        protected $host;

        /**
        * Returns the server hostname.
        * Needs to be implemented by the DynamicRequestInterface.
         * @return the server hostname
        */
        public function getHost()
        {
            if ($this->host === null) {
                $this->host = $this->getServerVar('SERVER_NAME');
            }

            return $this->host;
        }

        /**
         * Sets the host name.
         * Needs to be implemented by the DynamicRequestInterface.
         * @param string $host the host name to set
         * @return \Brickoo\Library\Cli\Request
         */
        public function setHost($host)
        {
            TypeValidator::IsString($host);

            $this->host = $host;

            return $this;
        }

        /**
         * Holds the request format.
         * @var string
         */
        protected $format;

        /**
         * Returns the request format.
         * Needs to be implemented by the DynamicRequestInterface.
         * @return string the request format or null if not set
         */
        public function getFormat()
        {
            return $this->format;
        }

        /**
         * Sets the request format.
         * Needs to be implemented by the DynamicRequestInterface.
         * @param string $format the request format
         * @return \Brickoo\Library\Http\Request
         */
        public function setFormat($format)
        {
            TypeValidator::IsString($format);

            $this->format = $format;

            return $this;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->arguments = array();
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

    }