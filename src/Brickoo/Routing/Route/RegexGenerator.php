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

    namespace Brickoo\Routing\Route;

    /**
     * RegexGenerator
     *
     * Implementation of a route regular expression generator.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RegexGenerator implements Interfaces\RegexGenerator {

        /** @var array */
        private $aliases;

        /**
         * Class constructor.
         * @param array $aliases the routing aliases
         * @return void
         */
        public function __construct(array $aliases = array()) {
            $this->aliases = $aliases;
        }

        /** {@inheritDoc} */
        public function generatePathRegex(\Brickoo\Routing\Interfaces\Route $Route) {
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
                    if (strpos($routePath, "/{". $parameterName ."}") !== false) {
                        $routePath = str_replace("/{". $parameterName ."}",
                            ($Route->hasDefaultValue($parameterName) ?
                                "(/(?<". $parameterName .">(". $Route->getRule($parameterName) .")?))?" :
                                "/(?<". $parameterName .">". $Route->getRule($parameterName) .")"
                            ),
                            $routePath
                        );
                    }
                    else {
                        $routePath = str_replace("{". $parameterName ."}",
                            ($Route->hasDefaultValue($parameterName) ?
                                "(?<". $parameterName .">(". $Route->getRule($parameterName) .")?)" :
                                "(?<". $parameterName .">". $Route->getRule($parameterName) .")"
                            ),
                            $routePath
                        );
                    }
                }
            }
        }

    }