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
         * Returns the route path listening to.
         * @throws UnexpectedValueException if the path is null
         * @return string the route path listening to
         */
        public function getPath();

        /**
         * Sets the route path to listen to.
         * @param string $path the path to liste to
         * @return \Brickoo\Library\Routing\Route
         */
        public function setPath($path);

        /**
         * Returns the accepted request formats.
         * @return string the accepted format or null if accepting all
         */
        public function getFormat();

        /**
         * Sets the request accepted formats.
         * @param string $format the format as regular expression like json|xml
         * @return \Brickoo\Library\Routing\Route
         */
        public function setFormat($format);

        /**
         * Returns the controller configuration.
         * @throws UnexpectedValueException if the controller is null
         * @return array the controller configuration
         */
        public function getController();

        /**
         * Sets the controller::method as callback to execute.
         * The controller and method can be a string for static calls
         * or an array for regular object initialization.
         * @param string $controller the controller namespace path
         * @param string $method the method name
         * @param boolean $static flag to mark the call as static
         * @return \Brickoo\Library\Routing\Route
         */
        public function setController($controller, $method, $static = false);

        /**
         * Returns the listening request method.
         * If the route is only available for cli requests is must be set to LOCAL.
         * @throws UnexpectedValueException if the method is null
         * @return string the request method listening
         */
        public function getMethod();

        /**
         * Sets the request method to listen to.
         * The method could be a regular expression like GET|POST.
         * @param string $method the request method to listen
         * @return \Brickoo\Library\Routing\Route
         */
        public function setMethod($method);

        /**
         * Returns the hostname listening to.
         * @return string if the hostname is set otherwise null
         */
        public function getHostname();

        /**
         * Sets the request method to listen to.
         * The hostname could be a regular expression like ([a-z]+\.)?domain\.com
         * @param string $method the request method to listen
         * @return \Brickoo\Library\Routing\Route
         */
        public function setHostname($hostname);

        /**
         * Checks if the session should be available.
         * @return boolean check result
         */
        public function isSessionEnabled();

        /**
         * Enables the session usage.
         * @return \Brickoo\Library\Routing\Route
         */
        public function enableSession();

        /**
         * Returns the session configuration to use.
         * @return array the session configuration to use
         */
        public function getSessionConfiguration();

        /**
         * Sets the session configuration to use.
         * The array keys which have effect are:
         * - id for session_id()
         * - name  for session_name()
         * - limiter for session_set_limiter()
         * @param array $configuration the session configuration
         * @return \Brickoo\Library\Routing\Route
         */
        public function setSessionConfiguration(array $configuration);

        /**
         * Checks if the response is cacheable.
         * @return boolean check result
         */
        public function isCacheable();

        /**
         * Enables or disables the response cache.
         * @return \Brickoo\Library\Routing\Route
         */
        public function enableCache();

        /**
         * Returns the response cache lifetime.
         * @return the response cache lifetime in seconds
         */
        public function getCacheLifetime();

        /**
         * Sets the response cache lifetime in seconds
         * @param integer $lifetime the response cache lifetime
         * @return \Brickoo\Library\Routing\Route
         */
        public function setCacheLifetime($lifetime);

        /**
         * Returns all the default values available.
         * @return array the default key-values if any
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
         * @return \Brickoo\Library\Routing\Route
         */
        public function addDefaultValue($parameterName, $defaultValue);

        /**
         * Returns all the regular expression rules available.
         * @return array the regular expression rules if any
         */
        public function getRules();

        /**
         * Returns the regular expression rule for the passed parameter name.
         * @param string $parameterName the parameter name to retrieve the rule for
         * @throws UnexpectedValueException if the parameter name has not an defualt value
         * @return string the rule assigned to the parameter name
         */
        public function getRule($parameterName);

        /**
         * Adds a regular expression rule to a parameter name.
         * @param string $parameterName the parameter name to add the rule to
         * @param string $rule the rule to add
         * @return \Brickoo\Library\Routing\Route
         */
        public function addRule($parameterName, $rule);

        /**
         * Checks if the parameter has an rule to match.
         * @param string $parameterName the parameter name to check
         * @return boolean check result
         */
        public function hasRule($parameterName);

    }