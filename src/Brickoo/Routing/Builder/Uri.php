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

    namespace Brickoo\Routing\Builder;

    use \Brickoo\Http\Request\Factory\Uri as UriFactory,
        Brickoo\Validator\Argument;

    /**
     * Uri
     *
     * Implements a uri builder to create a route matching uri.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Uri {

        /** @var string */
        private $location;

        /** @var \Brickoo\Routing\Interfaces\Router */
        private $Router;

        /** @var array */
        private $pathParameters;

        /** @var string */
        private $queryString;

        /**
         * Class constructor.
         * @param \Brickoo\Routing\Interfaces\Router $Router
         * @param string $location the base location e.g. http://localhost:8080
         * @return void
         */
        public function __construct(\Brickoo\Routing\Interfaces\Router $Router, $location) {
            Argument::IsString($location);
            $this->Router = $Router;
            $this->location = $location;
            $this->pathParameters = array();
            $this->queryString = "";
        }

        /** {@inheritDoc} */
        public function setPathParameters(array $pathParameters) {
            $this->pathParameters = $pathParameters;
            return $this;
        }

        /** {@inheritDoc} */
        public function setQueryString($queryString) {
            Argument::IsString($queryString);
            $this->queryString = $queryString;
            return $this;
        }

        /** {@inheritDoc} */
        public function build($routeName) {
            Argument::IsString($routeName);

            $Route = $this->Router->getRoute($routeName);

            $expectedPath = $this->getExpectedRoutePath($Route);

            if (! preg_match_all($this->getRegexFromRoute($Route), $expectedPath, $matches)) {
                throw new Exceptions\PathNotValid($routeName, $expectedPath);
            }

            $queryString = empty($this->queryString) ? "" : "?". ltrim($this->queryString, "?");
            $uri = rtrim($this->location, "/") . $expectedPath. $queryString;

            return UriFactory::CreateFromString($uri);
        }

        /**
         * Returns the expected uri path to validate against the route path.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @throws Exceptions\RequiredParametersMissing if a required parameter is missing
         * @return string the uri path expected
         */
        private function getExpectedRoutePath(\Brickoo\Routing\Interfaces\Route $Route) {
            $routePath = $Route->getPath();
            $pathParameters = array_merge($this->pathParameters, $Route->getDefaultValues());
            foreach ($pathParameters as $parameter => $value) {
                $routePath = str_replace("{". $parameter ."}", $value, $routePath);
            }

            if (preg_match_all("~(\{(?<missingParameters>[\w]+)\})~", $routePath, $matches)) {
                throw new Exceptions\RequiredParametersMissing($Route->getName(), $matches["missingParameters"]);
            }

            return $routePath;
        }

        /**
         * Returns a regular expression from the route to match a request path.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @return string the regular expression for the route
         */
        private function getRegexFromRoute(\Brickoo\Routing\Interfaces\Route $Route) {
            $routePath  = $Route->getPath();

            if (preg_match_all("~(\{(?<parameters>[\w]+)\})~", $routePath, $matches)) {
                $this->replaceRoutePathWithRulesExpressions($Route, $routePath, $matches['parameters']);
            }

            return "~^/". trim($routePath, "/") ."$~i";
        }

        /**
         * Replaces the route parameters with the rules defined.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @param string $regex the regular expression to modify
         * @param array $parameters the dynamic parameters of the route
         * @return void
         */
        private function replaceRoutePathWithRulesExpressions(\Brickoo\Routing\Interfaces\Route $Route, &$routePath, array $parameters) {
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