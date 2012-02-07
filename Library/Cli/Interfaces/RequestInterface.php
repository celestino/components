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

    namespace Brickoo\Library\Cli\Interfaces;

    /**
     * RequestInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface RequestInterface
    {

        /**
         * Returns the available cli arguments.
         * @return array the avialable cli arguments
         */
        public function getArguments();

        /**
         * Returns the passed arguement index value.
         * @param string|integer $index the index of the arguments to retrieve
         * @param mixed $defaultValue the default value if the index does not exist
         * @throws InvalidArgumentException if the index is not valid
         * @return string the argument value or the mixed default value
         */
        public function getArgument($index, $defaultValue = null);

        /**
         * Sets the array values as cli arguments keys.
         * The order is assigned as the passed array values are given.
         * If the argument is not available, null will be assigned
         * @param array $keys the keys to assign arguments to by order
         * @throws InvalidArgumentException if the index is not valid
         * @return \Brickoo\Library\Cli\Interfaces\CliInterface
         */
        public function setArgumentsKeys(array $keys);

        /**
         * Checks if the request has passed cli arguments.
         * @return boolean result
         */
        public function hasArguments();

        /**
         * Returns the number of the request passed cli arguments.
         * @return integer the number of arguments passed
         */
        public function countArguments();

        /**
         * Returns the server variable value if available.
         * @param string $name the server variable name to retrieve the value from
         * @param mixed $defaultValue the default value to return
         * @return string the value of the server variable otherwise mixed the default value
         */
        public function getServerVar($name, $defaultValue = null);

    }