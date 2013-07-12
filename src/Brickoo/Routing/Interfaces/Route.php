<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * Defines a route for handling dynamic requests.
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
         * @return string the route path listening to
         */
        public function getPath();

        /**
         * Returns the controller responsible for handling this route.
         * @return string the controller class name
         */
        public function getController();

        /**
         * Returns the action method of the responsible controller.
         * @return string the controller action method
         */
        public function getAction();

        /**
         * Returns the http method(s) allowed listening to.
         * @return string the http methods allowed as a regular expression
         */
        public function getMethod();

        /**
         * Returns the hostname(s) allowed listening to.
         * @return string the hostnames allowed as a regular expression
         */
        public function getHostname();

        /**
         * Returns the scheme allowed listening to.
         * @return string the scheme allowed as a regular expression
         */
        public function getScheme();

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
         * @param string $parameter the parameter name to retrieve the holding rule
         * @throws \UnexpectedValueException if the parameter does not exist
         * @return string the rule assigned to the parameter name
         */
        public function getRule($parameter);

        /**
         * Checks if the parameter has a rule to match.
         * @param string $parameter the parameter name to check
         * @return boolean check result
         */
        public function hasRule($parameter);

        /**
         * Returns all the parameters default values available.
         * @return array the default key-values if any
         */
        public function getDefaultValues();

        /**
         * Returns the default value of the passed parameter name.
         * @param string $parameter the parameter name
         * @throws \UnexpectedValueException if the parameter has not a default value
         * @return mixed the default value for the passed parameter name
         */
        public function getDefaultValue($parameter);
        /**
         * Checks if the rule has a default value.
         * @param string $parameter the parameter name to check
         * @return boolean check result
         */
        public function hasDefaultValue($parameter);

    }