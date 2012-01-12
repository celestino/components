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
         * Holds an instance of the Core Request class.
         * @see Brickoo\Library\Core\Request
         * @var object
         */
        protected $CoreRequest;

        /**
         * Lazy initialization of the Core\Request instance.
         * Returns the Core\Request instance.
         * @return object Core\Request implementing the Core\Interfaces\RequestInterface
         */
        public function getCoreRequest()
        {
            if (! $this->CoreRequest instanceof Core\Interfaces\RequestInterface)
            {
                $this->injectCoreRequest(new Core\Request());
            }

            return $this->CoreRequest;
        }

        /**
         * Injects the Core\Request dependency.
         * @param \Brickoo\Library\Core\Interfaces\RequestInterface $CoreRequest the Core\Request instance
         * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependecy
         * @return object reference
         */
        public function injectCoreRequest(\Brickoo\Library\Core\Interfaces\RequestInterface $CoreRequest)
        {
            if ($this->CoreRequest !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('Core\Interfaces\RequestInterface');
            }

            $this->CoreRequest = $CoreRequest;

            return $this;
        }

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
            if (empty($this->arguments))
            {
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

            if (empty($this->arguments))
            {
                $this->collectArguments();
            }

            if (array_key_exists($index, $this->arguments))
            {
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
         * @return object reference
         */
        public function setArgumentsKeys(array $keys)
        {
            $keys = array_values($keys);

            if (empty($this->arguments))
            {
                $this->collectArguments();
            }

            foreach($keys as $index => $value)
            {
                if(array_key_exists($index, $this->arguments))
                {
                    $this->arguments[$value] = $this->arguments[$index];
                }
                else
                {
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
            if
            (
                ($arguments = $this->getCoreRequest()->getServerVar('argv'))
                &&
                is_array($arguments)
            )
            {
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

        /**
         * Holds the request path assigned.
         * @var string
         */
        protected $requestPath;

        /**
         * Returns the request path used.
         * @throws UnexpectedValueException if the request path is not set
         * @return string the request path
         */
        public function getRequestPath()
        {
            if ($this->requestPath === null)
            {
                throw new \UnexpectedValueException('The request path is `null`.');
            }

            return $this->requestPath;
        }

        /**
         * Sets the request path to use.
         * @param string $requestPath the request path to use
         * @return object reference
         */
        public function setRequestPath($requestPath)
        {
            TypeValidator::IsString($requestPath);

            $this->requestPath = $requestPath;

            return $this;
        }

        /**
         * Returns always LOCAL as return method.
         * This is fix because cli modules should be never accessed
         * directly over the the web with GET methods.
         * @return string LOCAL as request method
         */
        public function getRequestMethod()
        {
            return 'LOCAL';
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

    }

?>