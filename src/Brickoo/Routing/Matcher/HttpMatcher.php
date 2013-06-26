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

    namespace Brickoo\Routing\Matcher;

    /**
     * HttpMatcher
     *
     * Implementation of a route matching agains a http request.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HttpMatcher implements Interfaces\Matcher {

        /** @var \Brickoo\Http\Interfaces\Request */
        private $Request;

        /** @var \Brickoo\Routing\Route\Interfaces\RegexGenerator */
        private $RegexGenerator;

        /** @var null|array */
        private $routeParameters;

        /** @var null|array */
        private $pathParameters;

        /**
         * Class constructor
         * @param \Brickoo\Http\Interfaces\Request $Request
         * @param array $aliases the route aliases as key/alias pairs
         * @return void
         */
        public function __construct(
            \Brickoo\Http\Interfaces\Request $Request,
            \Brickoo\Routing\Route\Interfaces\RegexGenerator $RegexGenerator
        ){
            $this->Request = $Request;
            $this->RegexGenerator = $RegexGenerator;
        }

        /** {@inheritDoc} */
        public function matches(\Brickoo\Routing\Interfaces\Route $Route) {
            if (! $this->isAllowedRoute($Route)) {
                return false;
            }

            $this->pathParameters = null;
            $this->routeParameters = null;

            if ($doesMatch = $this->isMatchingRoute($Route)) {
                $this->routeParameters = $this->getRouteParameters($Route);
            }

            return $doesMatch;
        }

        /** {@inheritDoc} */
        public function getParameters() {
            return $this->routeParameters ?: array();
        }

        /**
         * Checks if the Route is allowed to be executed.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @return boolean check result
         */
        private function isAllowedRoute(\Brickoo\Routing\Interfaces\Route $Route) {
            return (preg_match("~^(". $Route->getMethod() .")$~i", $this->Request->getMethod()) == 1
                && (
                    (($hostname = $Route->getHostname()) === null)
                    || preg_match("~^(". $hostname .")$~i", $this->Request->getUri()->getHostname()) == 1
                )
                && (
                    (($scheme = $Route->getScheme()) === null)
                    || preg_match("~^(". $scheme .")$~i", $this->Request->getUri()->getScheme()) == 1
                )
            );
        }

        /**
         * Checks if the route does match the request uri path.
         * On matching, the path paramters will be extracted.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @return boolean check result
         */
        private function isMatchingRoute(\Brickoo\Routing\Interfaces\Route $Route) {
            return (preg_match($this->RegexGenerator->generatePathRegex($Route),
                $this->Request->getUri()->getPath(), $this->pathParameters) == 1);
        }

        /**
         * Returns the route matched parameters for the current request.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @param array $pathMatchedParameters the path matching parameters
         * @return array the path parameters of the matching route
         */
        private function getRouteParameters(\Brickoo\Routing\Interfaces\Route $Route) {
            $routeParameters = array();

            if ($Route->hasRules()) {
                foreach(array_keys($Route->getRules()) as $ruleParameter) {
                    if (isset($this->pathParameters[$ruleParameter]) && (! empty($this->pathParameters[$ruleParameter]))) {
                        $routeParameters[$ruleParameter] = $this->pathParameters[$ruleParameter];
                    }
                    elseif ($Route->hasDefaultValue($ruleParameter)) {
                        $routeParameters[$ruleParameter] = $Route->getDefaultValue($ruleParameter);
                    }
                }
            }

            return $routeParameters;
        }

    }