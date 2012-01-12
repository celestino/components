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

    namespace Brickoo\Library\Routing;

    use Brickoo\Library\Core;
    use Brickoo\Library\Routing\Interfaces;
    use Brickoo\Library\Routing\Exceptions;

    /**
     * Router
     *
     * Router class which handles the routes collected.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Router implements Interfaces\RouterInterface
    {

        /**
         * Holds the Request object implementing the Core\Interfaces\DynamicRequestInterface
         * @var object
         */
        protected $Request;

        /**
         * Returns the Request instance implementing the Core\Interfaces\DynamicRequestInterface.
         * @return object Request instance implementing the Core\Interfaces\DynamicRequestInterface
         */
        public function getRequest()
        {
            return $this->Request;
        }

        /**
         * Holds the injected RouteCollecton instance.
         * @var Brickoo\Library\Routing\Interfaces\RouteCollectionInterface
         */
        protected $RouteCollection;

        /**
         * Lazy initialization of the RouteCollection dependecy.
         * Returns the injected RouteCollection dependecy.
         * @return object RouteCollection implementing the RouteCollectionIterface
         */
        public function getRouteCollection()
        {
            if (! $this->RouteCollection instanceof Interfaces\RouteCollectionInterface)
            {
                $this->injectRouteCollection(new RouteCollection());
            }

            return $this->RouteCollection;
        }

        /**
         * Injects the ROuteCollection dependency containign the assigned routes.
         * @param \Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection the colection of routes
         * @throws Exceptions\DependencyOverwriteException if the dependency is trying to overwrite
         * @return object reference
         */
        public function injectRouteCollection(\Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection)
        {
            if ($this->RouteCollection !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('RouteCollectionInterface');
            }

            $this->RouteCollection = $RouteCollection;

            return $this;
        }

        /**
         * Holds the requeste Route instance.
         * @var Brickoo\Library\Routing\Interfaces\Route
         */
        protected $RequestRoute;

        /**
         * Sets the requested Route for further routing.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route matched the request
         * @throws Exceptions\DependencyOverwriteException if trying to overwrite the requested route
         * @return object reference
         */
        public function setRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            if ($this->RequestRoute !== null)
            {
                throw new Core\Exceptions\ValueOverwriteException('RequestRoute');
            }

            $this->RequestRoute = $Route;

            return $this;
        }

        /**
         * Checks if the requested Route has been found and is set.
         * @return boolean check result
         */
        public function hasRequestRoute()
        {
            return ($this->RequestRoute instanceof Interfaces\RouteInterface);
        }


        /**
         * Checks if the Route matches the request path and method.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the Route to check
         * @return boolean check result
         */
        public function isRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            return
            (
                ($pathRegex = $this->getRegexFromPath($Route))
                &&
                preg_match($pathRegex, $this->getRequest()->getRequestPath())
                &&
                ($methodRegex = $this->getRegexFromMethod($Route))
                &&
                preg_match($methodRegex, $this->getRequest()->getRequestMethod())
            );
        }

        /**
         * Returns the request matching route.
         * @throws \UnexpectedValueException if the RouteCollections does not implement the ArrayIterator interface
         * @throws Routing\Exceptions\RequestedHasNoRouteException if the request has not a matching Route
         * @return object the request responsible Route implementing the RouteInterface
         */
        public function getRequestRoute()
        {
            if ($this->hasRequestRoute())
            {
                return $this->RequestRoute;
            }

            if (! ($Collection = $this->getRouteCollection()->getIterator()) instanceof \ArrayIterator)
            {
                throw new \UnexpectedValueException('The RouteCollection does not implement the ArrayIterator interface.');
            }

            foreach ($Collection as $Route)
            {
                if ($this->isRequestRoute($Route))
                {
                    $this->setRequestRoute($Route);
                    break;
                }
            }

            if (! $this->hasRequestRoute())
            {
                throw new Exceptions\RequestHasNoRouteException($this->getRequest()->getRequestPath());
            }

            return $this->getRequestRoute();
        }

        /**
        * Class constructor.
        * Injects a Request dependency implementing the Core}Interfaces\DynamicRequestInterface
        * Initializes the class properties.
        * @return void
        */
        public function __construct(\Brickoo\Library\Core\Interfaces\DynamicRequestInterface $Request)
        {
            $this->Request            = $Request;
            $this->RequestRoute       = null;
            $this->RouteCollection    = null;
        }

        /**
         * Returns a regular expression from the route method.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route to use
         * @return string the regular expression for the request method
         */
        public function getRegexFromMethod(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            return '~^(' . $Route->getMethod() . ')$~i';
        }

        /**
         * Returns a regular expression from the route path and rules or default values available.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route to use
         * @return string the regular expresion for the request path
         */
        public function getRegexFromPath(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            $regex = $Route->getPath();

            if (preg_match_all('~(\{(?<parameters>[\w]+)\})~', $Route->getPath(), $matches))
            {
                foreach ($matches['parameters'] as $parameterName)
                {
                    if ($Route->hasRule($parameterName))
                    {
                        $regex = str_replace
                        (
                            '{' . $parameterName . '}',
                            '(?<'. $parameterName . '>' .$Route->getRule($parameterName) . ')',
                            $regex
                        );
                        continue;
                    }

                    if ($Route->hasDefaultValue($parameterName))
                    {
                        $regex = str_replace
                        (
                            '{' . $parameterName . '}',
                            '([^/]+)?.*',
                            $regex
                        );
                        continue;
                    }

                    $regex = str_replace('{' . $parameterName . '}', uniqid($parameterName), $regex);
                }
            }

            return '~^/' . trim($regex, '/') . '$~i';
        }

    }

?>