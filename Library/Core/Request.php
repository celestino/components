<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    namespace Brickoo\Library\Core;

    use Brickoo\Library\Http;
    use Brickoo\Library\Cli;
    use Brickoo\Library\Core\Exceptions;
    use Brickoo\Library\Core\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Request
     *
     * Holds the request arguments and provides utility methods.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class Request implements Interfaces\RequestInterface
    {

        /**
         * Holds an object implementing the RequestInterface.
         * @see Bricko\Library\Cli\Interfaces\CliRequestInterface
         * @var object
         */
        protected $_Cli;

        /**
         * Returns the Cli Request object.
         * If the object does not exist, it wll be created.
         * @return object Cli implementing the Cli\Interfaces\RequestInterface
         */
        public function Cli()
        {
            if (! $this->_Cli instanceof Cli\Interfaces\RequestInterface)
            {
                $this->injectCliRequest(new Cli\Request($this));
            }

            return $this->_Cli;
        }

        /**
         * Lazy initialization of the Cli Request instance.
         * @param Cli\Interfaces\RequestInterface $Cli the Cli object implementing the RequestInterface
         * @throws DependencyOverrideException if trying to override the dependency
         * @return object reference
         */
        public function injectCliRequest(\Brickoo\Library\Cli\Interfaces\RequestInterface $Cli)
        {
            if ($this->_Cli !== null)
            {
                throw new Exceptions\DependencyOverrideException('Cli\Interfaces\RequestInterface');
            }

            $this->_Cli = $Cli;

            return $this;
        }

        /**
         * Holds an instance of the Http Request class.
         * @see Bricko\Library\HTTP\Interfaces\RequestInterface
         * @var object
         */
        protected $_Http;

        /**
         * Returns the Http Request object.
         * If the object does not exist, it wll be created.
         * @return object Http implementing the Http\Interfaces\RequestInterface
         */
        public function Http()
        {
            if (! $this->_Http instanceof Http\Interfaces\RequestInterface)
            {
                $this->injectHttpRequest(new Http\Request($this));
            }

            return $this->_Http;
        }

        /**
         * Lazy initialization of the Http Request instance.
         * @param Http\Interfaces\RequestInterface $Http the Http object implementing the RequestInterface
         * @throws DependencyOverrideException if trying to override the dependency
         * @return object reference
         */
        public function injectHttpRequest(\Brickoo\Library\Http\Interfaces\RequestInterface $Http)
        {
            if ($this->_Http !== null)
            {
                throw new Exceptions\DependencyOverrideException('Http\Interfaces\RequestInterface');
            }

            $this->_Http = $Http;

            return $this;
        }

        /**
         * Holds the transformed server variables.
         * @var array
         */
        protected $serverVars;

        /**
         * Collects the server variables with transformation.
         * @return object reference
         */
        protected function collectServerVars()
        {
            $serverVars = array();

            foreach($_SERVER as $key => $value)
            {
                $key = strtoupper(str_replace(array('-', ' ', '.'), '_', $key));
                $serverVars[$key] = $value;
            }

            $this->serverVars = $serverVars;

            return $this;
        }

        /**
         * Returns the server variable value of the passed key.
         * @param string $keyName the key name to return the value from
         * @param mixed $defaultValue the default value to return if unset
         * @throws InvalidArgumentException if the key or apache flag is not valid
         * @return string/mixed the header or default value
         */
        public function getServerVar($keyName, $defaultValue = null)
        {
            TypeValidator::Validate('isString', array($keyName));

            if (empty($this->serverVars))
            {
                $this->collectServerVars();
            }

            $keyName = strtoupper(str_replace(array('-', ' ', '.'), '_', $keyName));

            if (array_key_exists($keyName, $this->serverVars))
            {
                return $this->serverVars[$keyName];
            }

            return $defaultValue;
        }

        /**
         * Checks if the passed interface is used.
         * @param string $interface the interface to check
         * @return boolean check result
         */
        public function isPHPInterface($interface)
        {
            TypeValidator::Validate('useRegex', array(array('~[a-z\-23]+~i', $interface)));

            return (strpos(PHP_SAPI, $interface) !== false);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->clear();
        }

        /**
         * Clears the object properties.
         * @return object reference
         */
        public function clear()
        {
            $this->_Http         = null;
            $this->_Cli          = null;
            $this->serverVars    = array();

            return $this;
        }

    }

?>