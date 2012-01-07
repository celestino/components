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
     * RouterInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface RouterInterface
    {

        /**
         * Returns the Request instance implementing the Core\Interfaces\DynamicRequestInterface
         * @return object Request instance implementing the Core\Interfaces\DynamicRequestInterface
         */
        public function getRequest();

        /**
         * Lazy initialization of the RouteCollection dependecy.
         * Returns the injected RouteCollection dependecy.
         * @return object RouteCollection implementing the RouteCollectionIterface
         */
        public function getRouteCollection();

        /**
         * Injects the ROuteCollection dependency containign the assigned routes.
         * @param \Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection the colection of routes
         * @throws Exceptions\DependencyOverwriteException if the dependency is trying to overwrite
         * @return object reference
         */
        public function injectRouteCollection(\Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection);

        /**
         * Sets the requested Route for further routing.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route matched the request
         * @throws Exceptions\DependencyOverwriteException if trying to overwrite the requested route
         * @return object reference
         */
        public function setRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route);

        /**
         * Checks if the requested Route has been found and is set.
         * @return boolean check result
         */
        public function hasRequestRoute();


        /**
         * Checks if the Route matches the request path and method.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the Route to check
         * @return boolean check result
         */
        public function isRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route);

        /**
         * Returns the request matching route.
         * @throws \UnexpectedValueException if the RouteCollections does not implement the ArrayIterator interface
         * @throws Routing\Exceptions\RequestedHasNoRouteException if the request has not a matching Route
         * @return object the request responsible Route implementing the RouteInterface
         */
        public function getRequestRoute();

        /**
        * Resets the object properties.
        * @return object reference
        */
        public function reset();

        /**
         * Returns a regular expression from the route method.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route to use
         * @return string the regular expression for the request method
         */
        public function getRegexFromMethod(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route);

        /**
         * Returns a regular expression from the route path and rules or default values available.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route to use
         * @return string the regular expresion for the request path
         */
        public function getRegexFromPath(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route);

    }

?>