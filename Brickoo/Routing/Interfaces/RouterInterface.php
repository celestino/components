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
         * Returns the cache file name.
         * @return string the cache file name
         */
        public function getCacheFilename();

        /**
         * Sets the cache file name.
         * @param string $cacheFilename the cache file name
         * @return \Brickoo\Library\Routing\Router
         */
        public function setCacheFilename($cacheFilename);

        /**
         * Returns the cache directory used.
         * @throws UnexpectedValueException if the cache directory is not set
         * @return string the cache directory
         */
        public function getCacheDirectory();

        /**
         * Sets the cache directory to use.
         * @param string $cacheDirectory the cache directory to use
         * @return \Brickoo\Library\Routing\Router
         */
        public function setCacheDirectory($cacheDirectory);

        /**
         * Checks if the cache directory is set.
         * @return boolean check result
         */
        public function hasCacheDirectory();

        /**
         * Returns the routes file name.
         * @return string the routes file name
         */
        public function getRoutesFilename();

        /**
         * Sets the  routes file name searched in the modules directory.
         * @param string $routesFilename the routes file name
         * @return \Brickoo\Library\Routing\Router
         */
        public function setRoutesFilename($routesFilename);

        /**
         * Returns the modules available.
         * @return array the modules available
         */
        public function getModules();

        /**
         * Sets the modules to load the routes from if available.
         * If the modules a set directly, the modules will not be available through the Brickoo Registry.
         * @param array $modules the modules to load the routes from
         * @throws Core\Exceptions\ValueOverwriteException if trying to overwrite the available modules
         * @return \Brickoo\Library\Routing\Router
         */
        public function setModules(array $modules);

        /**
         * Checks if any modules are available.
         * @return boolean check result
         */
        public function hasModules();

        /**
         * Returns the Request instance implementing the Core\Interfaces\RequestInterface.
         * @return \Brickoo\Library\Core\Interfaces\RequestInterface
         */
        public function getRequest();

        /**
         * Lazy initialization of the RouteCollection dependecy.
         * @param \Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection the colection of routes
         * @return \Brickoo\Library\Routing\Interfaces\RouterCollectionInterface
         */
        public function RouteCollection(\Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection = null);

        /**
         * Sets the requested Route for further routing.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route matched the request
         * @throws \Brickoo\Library\Core\Exceptions\ValueOverwriteException if trying to overwrite the request route
         * @return \Brickoo\Library\Routing\Router
         */
        public function setRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route);

        /**
         * Checks if the requested Route has been found and is set.
         * @return boolean check result
         */
        public function hasRequestRoute();

        /**
         * Checks if the Route matches the request.
         * @return boolean check result
         */
        public function isRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route);

        /**
         * Checks if the cached route matches the request.
         * @param array $route the route configuration to check
         * @return boolean check result
         */
        public function isCachedRequestRoute(array $route);

        /**
         * Returns the request matching route.
         * If the CacheManager is available the proceded routes will be cached.
         * @throws Routing\Exceptions\RequestedHasNoRouteException if the request has not a matching Route
         * @return \Brickoo\Library\Routing\Route
         */
        public function getRequestRoute();

        /**
         * Collectes the routes available to add to the RouteCollection.
         * Searches through all available modules available to require the route collections.
         * This requires the registered modules, which is normaly done by the FrontController.
         * @return void
         */
        public function collectModulesRoutes();

        /**
         * Returns the parsed routes for caching purpose.
         * @return array the parsed routes
         */
        public function getCompressedRoutes();

        /**
         * Loads the routes from the cache file and tries to find the request matching route.
         * This requires an available cache directory with read permission.
         * @return void
         */
        public function loadRoutesFromCache();

        /**
         * Saves the parsed routes to the cache directory.
         * This requires an available cache directory with write permission.
         * @return void
         */
        public function saveRoutesToCache();

        /**
         * Returns a regular expression from the route path and rules or default values available.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route to use
         * @return string the regular expresion for the request path
         */
        public function getRegexFromRoutePath(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route);

    }