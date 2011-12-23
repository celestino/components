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

    namespace Brickoo\Library\Cli;

    use Brickoo\Library\Core;
    use Brickoo\Library\Cli\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Request
     *
     * Request class for accessing cli arguments.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class Request implements Interfaces\RequestInterface
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
            TypeValidator::Validate('isStringOrInteger', array($index));

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
            TypeValidator::Validate('isArray', array($keys));

            $keys = array_values($keys);

            if (empty($this->arguments))
            {
                $this->collectArguments();
            }

            foreach($keys as $index => $value)
            {
                if
                (
                    is_array($this->arguments)
                    &&
                    array_key_exists($index, $this->arguments)
                )
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
         * collect the available arguments passed.
         * @return void
         */
        protected function collectArguments()
        {
            if
            (
                ($arguments = $this->Request->getServerVar('argv'))
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
         * Class constructor.
         * Initializes the class properties.
         * @param object Request implementing the RequestInterface
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
            $this->arguments = array();

            return $this;
        }

    }

?>