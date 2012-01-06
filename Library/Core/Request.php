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

    namespace Brickoo\Library\Core;

    use Brickoo\Library\Core\Exceptions;
    use Brickoo\Library\Core\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Request
     *
     * Holds the request arguments and provides utility methods.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Request implements Interfaces\RequestInterface
    {

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
            TypeValidator::IsString($keyName);

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
            TypeValidator::MatchesRegex('~[a-z\-23]+~i', $interface);

            return (strpos(PHP_SAPI, $interface) !== false);
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
         * Clears the object properties.
         * @return object reference
         */
        public function reset()
        {
            $this->serverVars    = array();

            return $this;
        }

    }

?>