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

    namespace Brickoo\Library\Routing\Interfaces;

    /**
     * RouteInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface RouteInterface
    {

        /**
        * Returns the route path listening.
        * @throws UnexpectedValueException if the path is null
        * @return string the route path listening
        */
        public function getPath();

        /**
         * Sets the route path to listen to.
         * @param string $path the path to liste to
         * @return object reference
         */
        public function setPath($path);

        /**
         * Returns the controller:method to execute.
         * @throws UnexpectedValueException if the controller is null
         * @return string the controller and method to execute
         */
        public function getController();

        /**
         * Sets the controller:method to execute.
         * The controller and method has to be seperated by ':'.
         * @param string $controller the controller and method to execute
         * @return object reference
         */
        public function setController($controller);

        /**
         * Returns the listening request method.
         * @throws UnexpectedValueException if the method is null
         * @return string the request method listening
         */
        public function getMethod();

        /**
         * Sets the request method to listen to.
         * @param string $method the request method to listen
         * @return object reference
         */
        public function setMethod($method);

        /**
         * Returns all the default values available.
         * @return array the default key-values
         */
        public function getDefaultValues();

        /**
         * Returns the default value of the passed parameter name.
         * @param string $parameterName the parameter name
         * @throws UnexpectedValueException if the parameter has not an default value
         * @return mixed the default value for the passed parameter name
         */
        public function getDefaultValue($parameterName);

        /**
         * Checks if the parameter has an default value.
         * @param string $parameterName the parameter to check
         * @return boolean check result
         */
        public function hasDefaultValue($parameterName);

        /**
         * Adds an parameter and his default value.
         * If the parameter name does exists it will be overwritten !
         * @param string $parameterName the parameter name to add the value to
         * @param mixed $defaultValue the default value to add
         * @return object reference
         */
        public function addDefaultValue($parameterName, $defaultValue);

        /**
         * Returns all the rules available.
         * @return array the rules
         */
        public function getRules();

        /**
         * Returns the rule for the passed parameter name.
         * @param string $parameterName the parameter name to retrieve the rule for
         * @throws UnexpectedValueException if the parameter name has not an defualt value
         * @return string the rule assigned to the parameter name
         */
        public function getRule($parameterName);

        /**
         * Checks if the parameter has an rule to match.
         * @param string $parameterName the parameter name to check
         * @return boolean check result
         */
        public function hasRule($parameterName);

        /**
        * Clears the class properties.
        * @return object reference
        */
        public function reset();

    }

?>