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

    namespace Brickoo\Routing\Interfaces;

    /**
     * Route
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Route {

        /**
         * Returns the unique name of the route.
         * @return string the unique route name
         */
        public function getName();

       /**
         * Returns the route path listening to.
         * @throws UnexpectedValueException if the path is null
         * @return string the route path listening to
         */
        public function getPath();

        /**
         * Returns the accepted request formats.
         * @return string the accepted format or null if accepting all
         */
        public function getFormat();

        /**
         * Returns the default format.
         * @return string the default format or null if none set.
         */
        public function getDefaultFormat();

        /**
         * Returns the controller configuration.
         * @throws UnexpectedValueException if the controller is null
         * @return array the controller configuration
         */
        public function getController();

        /**
         * Returns the listening request method.
         * If the route is only available for cli requests is must be set to LOCAL.
         * @throws UnexpectedValueException if the method is null
         * @return string the request method listening
         */
        public function getMethod();

        /**
         * Returns the hostname listening to.
         * @return string if the hostname is set otherwise null
         */
        public function getHostname();

        /**
         * Enables or the session usage.
         * @return \Brickoo\Routing\Route
         */
        public function requireSession();

        /**
         * Checks if a session is required.
         * @return boolean check result
         */
        public function isSessionRequired();

        /**
         * Checks if the response is cacheable.
         * @return boolean check result
         */
        public function isCacheable();

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
         * Returns all the regular expression rules available.
         * @return array the regular expression rules if any
         */
        public function getRules();

        /**
         * Checks if the route has rules.
         * @return boolean check result
         */
        public function hasRules();

        /**
         * Returns the regular expression rule for the passed parameter name.
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

    }