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

        /** @var array */
        private $aliases;

        /** @var array */
        private $matchedRouteParamaters;

        /**
         * Class constructor
         * @param \Brickoo\Http\Interfaces\Request $Request
         * @param array $aliases the route aliases as key/alias pairs
         * @return void
         */
        public function __construct(\Brickoo\Http\Interfaces\Request $Request, array $aliases = array()) {
            $this->Request = $Request;
            $this->aliases = $aliases;
            $this->matchedRouteParamaters = array();
        }

        /** {@inheritDoc} */
        public function matches(\Brickoo\Routing\Interfaces\Route $Route) {
            if (! $this->isAllowedRoute($Route)) {
                return false;
            }

            if ($matches = (preg_match($this->getRegexFromRoute($Route), $this->Request->getUri()->getPath(), $pathMatchedParameters) == 1)) {
                $this->matchedRouteParamaters = $this->getMatchedRoutePathParameters($Route, $pathMatchedParameters);
            }

            return $matches;
        }

        /** {@inheritDoc} */
        public function getParameters() {
            return $this->matchedRouteParamaters;
        }

        /**
         * Returns the route matched parameters for the current request.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @param array $pathMatchedParameters the path matching parameters
         * @return array the path parameters of the matching route
         */
        private function getMatchedRoutePathParameters(\Brickoo\Routing\Interfaces\Route $Route, array $pathMatchedParameters) {
            $routeParameters = array();

            if ($Route->hasRules()) {
                foreach(array_keys($Route->getRules()) as $ruleParameter) {
                    if (isset($pathMatchedParameters[$ruleParameter]) && (! empty($pathMatchedParameters[$ruleParameter]))) {
                        $routeParameters[$ruleParameter] = $pathMatchedParameters[$ruleParameter];
                    }
                    elseif ($Route->hasDefaultValue($ruleParameter)) {
                        $routeParameters[$ruleParameter] = $Route->getDefaultValue($ruleParameter);
                    }
                }
            }

            return $routeParameters;
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
         * Returns a regular expression from the route to match a request path.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @return string the regular expression for the route
         */
        private function getRegexFromRoute(\Brickoo\Routing\Interfaces\Route $Route) {
            $routePath  = $this->getRoutePath($Route);

            if (preg_match_all("~(\{(?<parameters>[\w]+)\})~", $routePath, $matches)) {
                $this->replaceRoutePathWithRulesExpressions($routePath, $matches['parameters'], $Route);
            }

            return "~^/". trim($routePath, "/") ."$~i";
        }

        /**
         * Returns the route path contaning the aliases definitions if any given.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @return string the modified route path containing the aliases
         */
        private function getRoutePath(\Brickoo\Routing\Interfaces\Route $Route) {
            $routePath = $Route->getPath();

            foreach ($this->aliases as $routeKey => $routeAlias) {
                if (strpos($routePath, $routeKey) !== false) {
                    $replacement = sprintf("(%s|%s)", $routeKey, preg_quote($routeAlias, "~"));
                    $routePath = str_replace($routeKey, $replacement, $routePath);
                    break;
                }
            }

            return $routePath;
        }

        /**
         * Replaces the route parameters with the rules defined.
         * @param string $regex the regular expression to modify
         * @param array $parameters the dynamic parameters of the route
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @return void
         */
        private function replaceRoutePathWithRulesExpressions(&$routePath, array $parameters, \Brickoo\Routing\Interfaces\Route $Route) {
            foreach ($parameters as $parameterName) {
                if ($Route->hasRule($parameterName)) {
                    $routePath = str_replace("/{". $parameterName ."}",
                        (
                            $Route->hasDefaultValue($parameterName) ?
                            "(/(?<". $parameterName .">(". $Route->getRule($parameterName) .")?))?" :
                            "/(?<". $parameterName .">". $Route->getRule($parameterName) .")"
                        ),
                        $routePath
                    );
                }
            }
        }

    }