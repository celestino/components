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

    namespace Brickoo\Routing;

    use Brickoo\Validator\TypeValidator;

    /**
     * RouteFinder
     *
     * Searches for the Route matching the Request.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouteFinder implements Interfaces\RouteFinderInterface
    {

        /**
         * Holds an instance of the RouteCollection class.
         * @var \Brickoo\Routing\Interfaces\RouteCollectionInterface
         */
        protected $RouteCollection;

        /**
         * Holds an instance of the Request class.s
         * @var \Brickoo\Core\Interfaces\RequestInterface
         */
        protected $Request;

        /**
         * Holds an instance of the Memory\Container class.
         * @var \Brickoo\Memory\Interfaces\ContainerInterface
         */
        protected $Aliases;

        /**
         * Class constructor.
         * Initializes the class properties.
         * Injects the dependencies.
         * @param \Brickoo\Routing\Interfaces\RouteCollectionInterface $RouteCollection
         * @param \Brickoo\Core\Interfaces\RequestInterface $Request
         * @param \Brickoo\Memory\Interfaces\ContainerInterface $Aliases
         * @return void
         */
        public function __construct(\Brickoo\Routing\Interfaces\RouteCollectionInterface $RouteCollection,
            \Brickoo\Core\Interfaces\RequestInterface $Request,
            \Brickoo\Memory\Interfaces\ContainerInterface $Aliases
        ){
            $this->Aliases            = $Aliases;
            $this->Request            = $Request;
            $this->RouteCollection    = $RouteCollection;
        }

        /**
         * Search for the Route which is responsible for the request.
         * @throws \Brickoo\Routing\Exceptions\RequestHasNoRouteException if no route matches the request
         * @return \Brickoo\Routing\Interface\RouteInterface
         */
        public function find(){
            $RequestRoute = null;

            foreach($this->RouteCollection->getRoutes() as $Route) {
                if (preg_match($this->getRegexFromRoutePath($Route), $this->Request->getPath(), $pathMatches) &&
                    $this->isAllowedRoute($Route)
                ){
                    $RequestRoute = $this->createRequestRoute($Route, $pathMatches);
                    break;
                }
            }

            if ($RequestRoute === null) {
                throw new Exceptions\RequestHasNoRouteException($this->Request->getPath());
            }

            return $RequestRoute;
        }

        /**
         * Returns the request route parameters.
         * @param \Brickoo\Routing\Interfaces\RouteInterface $Route
         * @param array $pathMatches the path matching fields
         * @return array the request route parameters
         */
        public function getRouteParameters(\Brickoo\Routing\Interfaces\RouteInterface $Route, array $pathMatches)
        {
            $routeParams = array();

            if ($Route->hasRules()) {
                foreach(array_keys($Route->getRules()) as $parameter) {
                    if (isset($pathMatches[$parameter]) && (! empty($pathMatches[$parameter]))) {
                        $routeParams[$parameter] = $pathMatches[$parameter];
                    }
                    elseif ($Route->hasDefaultValue($parameter)) {
                        $routeParams[$parameter] = $Route->getDefaultValue($parameter);
                    }
                }
            }

            if (isset($pathMatches['__FORMAT__'])) {
                $routeParams['format'] = $pathMatches['__FORMAT__'];
            }

            return $routeParams;
        }

        /**
         * Creates a RequestRoute from the matched Route.
         * @param \Brickoo\Routing\Interfaces\RouteInterface $Route
         * @param array $pathMatches the path matching fields
         * @return \Brickoo\Routing\RequestRoute
         */
        public function createRequestRoute(\Brickoo\Routing\Interfaces\RouteInterface $Route, array $pathMatches)
        {
            $routeParams = $this->getRouteParameters($Route, $pathMatches);

            if (! isset($routeParams['format'])) {
                $routeParams['format'] = ($defaultFormat = $Route->getDefaultFormat()) !== null ?
                $defaultFormat : $this->Request->getFormat();
            }

            $RequestRoute = new RequestRoute($Route);
            $RequestRoute->Params()->merge($routeParams);

            return $RequestRoute;
        }

        /**
         * Checks if the Route is allowed to be executed.
         * @paam \Brickoo\Routing\Interfaces\RouteInterface $Route
         * @return boolean check result
         */
        public function isAllowedRoute(\Brickoo\Routing\Interfaces\RouteInterface $Route)
        {
            return
            (
                preg_match('~^(' . $Route->getMethod() . ')$~i', $this->Request->getMethod()) &&
                (
                    (($hostname = $Route->getHostname()) === null) ||
                    preg_match('~^(' . $hostname . ')$~i', $this->Request->getHost())
                )
            );
        }

        /**
         * Returns the Route regular expression to add for matching formats.
         * @param \Brickoo\Routing\Interfaces\RouteInterface $Route the Route instance
         * @return string the regular expression of the route format
         */
        public function getRegexRouteFormat(\Brickoo\Routing\Interfaces\RouteInterface $Route)
        {
            $formatRegex = '(\..*)?';

            if (($routeFormat = $Route->getFormat()) !== null) {
                $formatRegex = '(\.(?<__FORMAT__>' . $routeFormat . '))?';
            }

            return $formatRegex;
        }

        /**
         * Returns the route path containg the aliases definitions.
         * @param \Brickoo\Routing\Interfaces\RouteInterface $Route the Route instance
         * @return string the modified route path containing the aliases
         */
        public function getRouteAliasesPath(\Brickoo\Routing\Interfaces\RouteInterface $Route)
        {
            $routePath = $Route->getPath();

            if (! $this->Aliases->isEmpty()) {
                $this->Aliases->rewind();
                while($this->Aliases->valid()) {
                    if (($position = strpos($routePath, ($key = $this->Aliases->key()))) === 1) {
                        $replacement = sprintf('(%s|%s)', $key, preg_quote($this->Aliases->current(), '~'));
                        $routePath = str_replace($key, $replacement, $routePath);
                        break;
                    }
                    $this->Aliases->next();
                }
            }

            return $routePath;
        }

        /**
         * Returns a regular expression from the route path and rules or default values available.
         * @param \Brickoo\Routing\Interfaces\RouteInterface $Route the route to use
         * @return string the regular expression for the request path
         */
        public function getRegexFromRoutePath(\Brickoo\Routing\Interfaces\RouteInterface $Route)
        {
            $regex  = $this->getRouteAliasesPath($Route);
            $regex .= $this->getRegexRouteFormat($Route);

            if (preg_match_all('~(\{(?<parameters>[\w]+)\})~', $regex, $matches)) {
                foreach ($matches['parameters'] as $parameterName) {
                    if ($Route->hasRule($parameterName)) {
                        $regex = str_replace(
                            '/{' . $parameterName . '}',
                            (
                                $Route->hasDefaultValue($parameterName) ?
                                '(/(?<' . $parameterName .'>(' . $Route->getRule($parameterName) . ')?))?' :
                                '/(?<'. $parameterName . '>' . $Route->getRule($parameterName) . ')'
                            ),
                            $regex
                        );
                        continue;
                    }

                    $regex = str_replace('{' . $parameterName . '}', md5($parameterName), $regex);
                }
            }

            return '~^/' . trim($regex, '/') . '$~i';
        }

    }