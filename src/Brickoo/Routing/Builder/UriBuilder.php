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

    namespace Brickoo\Routing\Builder;

    use Brickoo\Http\Request\Factory\UriFactory,
        Brickoo\Routing\Route\RegexGenerator,
        Brickoo\Validator\Argument;

    /**
     * UriBuilder
     *
     * Implements a uri builder to create a route matching uri.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UriBuilder implements Interfaces\UriBuilder {

        /** @var string */
        private $baseUrl;

        /** @var \Brickoo\Routing\Interfaces\Router */
        private $Router;

        /** @var \Brickoo\Routing\Route\Interfaces\RegexGenerator */
        private $RegexGenerator;

        /**
         * Class constructor.
         * @param \Brickoo\Routing\Interfaces\Router $Router
         * @param \Brickoo\Routing\Route\Interfaces\RegexGenerator $RegexGenerator
         * @param string $baseUrl the base url e.g. http://localhost:8080
         * @return void
         */
        public function __construct(\Brickoo\Routing\Interfaces\Router $Router, $baseUrl) {
            Argument::IsString($baseUrl);

            if (empty($baseUrl)) {
                throw new \InvalidArgumentException("The base url can not be empty.");
            }

            $this->Router = $Router;
            $this->baseUrl = $baseUrl;
        }

        /** {@inheritDoc} */
        public function setRegexGenerator(\Brickoo\Routing\Route\Interfaces\RegexGenerator $RegexGenerator) {
            $this->RegexGenerator = $RegexGenerator;
            return $this;
        }

        /** {@inheritDoc} */
        public function build($routeName, array $pathParameters, $queryString = null) {
            Argument::IsString($routeName);

            if ($queryString !== null) {
                Argument::IsString($queryString);
            }

            if (! $this->Router->hasRoute($routeName)) {
                throw new Exceptions\RouteNotFound($routeName);
            }

            $Route = $this->Router->getRoute($routeName);

            $expectedPath = $this->getExpectedRoutePath($Route, $pathParameters);

            if (! preg_match_all($this->getRegexGenerator()->generatePathRegex($Route), $expectedPath, $matches)) {
                throw new Exceptions\PathNotValid($routeName, $expectedPath);
            }

            return $this->createUriString($expectedPath, $queryString);
        }

        /**
         * Returns the expected uri path to validate against the route path.
         * @param \Brickoo\Routing\Interfaces\Route $Route
         * @param array $pathParameters the path parameters to use
         * @throws Exceptions\RequiredParametersMissing if a required parameter is missing
         * @return string the uri path expected
         */
        private function getExpectedRoutePath(\Brickoo\Routing\Interfaces\Route $Route, $pathParameters) {
            $routePath = $Route->getPath();
            $pathParameters = array_merge($pathParameters, $Route->getDefaultValues());

            foreach ($pathParameters as $parameter => $value) {
                $routePath = str_replace("{". $parameter ."}", $value, $routePath);
            }

            if (preg_match_all("~(\{(?<missingParameters>[\w]+)\})~", $routePath, $matches)) {
                throw new Exceptions\RequiredParametersMissing($Route->getName(), $matches["missingParameters"]);
            }

            return $routePath;
        }

        /**
         * Returns the regular route expression generator dependency.
         * If it does not exists it will be created using the framework implementation.
         * @return \Brickoo\Routing\Route\Interfaces\RegexGenerator
         */
        private function getRegexGenerator() {
            if ($this->RegexGenerator === null) {
                $this->RegexGenerator = new RegexGenerator();
            }
            return $this->RegexGenerator;
        }

        /**
         * Returns the created uri string.
         * @param string $uriPath the uri path
         * @param string $queryString the query string
         * @return string the created uri string
         */
        private function createUriString($uriPath, $queryString) {
            return rtrim($this->baseUrl, "/") . $uriPath. (empty($queryString) ? "" : "?". ltrim($queryString, "?"));
        }

    }