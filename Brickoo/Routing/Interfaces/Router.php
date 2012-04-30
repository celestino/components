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

    namespace Brickoo\Routing\Interfaces;

    /**
     * Router
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface Router {

        /**
         * Returns the Request instance implementing the Core\Interfaces\Request.
         * @return \Brickoo\Core\Interfaces\Request
         */
        public function getRequest();

        /**
         * Returns the routes file name.
         * @return string the routes file name
         */
        public function getRoutesFilename();

        /**
         * Sets the  routes file name searched in the modules directory.
         * @param string $routesFilename the routes file name
         * @return \Brickoo\Routing\Router
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
         * @return \Brickoo\Routing\Router
         */
        public function setModules(array $modules);

        /**
         * Checks if any modules are available.
         * @return boolean check result
         */
        public function hasModules();

        /**
         * Lazy initialization of the RouteCollection dependecy.
         * @param \Brickoo\Routing\Interfaces\RouteCollection $RouteCollection the colection of routes
         * @return \Brickoo\Routing\Interfaces\RouterCollection
         */
        public function RouteCollection(\Brickoo\Routing\Interfaces\RouteCollection $RouteCollection = null);

        /**
         * Lazy initialization of the RouteFinder dependecy.
         * @param \Brickoo\Routing\Interfaces\RouteFinder $RouteFinder the RouteFinder dependency
         * @return \Brickoo\Routing\Interfaces\RouteFinder
         */
        public function RouteFinder(\Brickoo\Routing\Interfaces\RouteFinder $RouteFinder = null);

        /**
         * Lazy initialization of the Aliases dependecy.
         * @param \Brickoo\Memory\Interfaces\Container $Aliases the Container dependency
         * @return \Brickoo\Memory\Interfaces\Container
         */
        public function Aliases(\Brickoo\Memory\Interfaces\Container $Aliases = null);

        /**
         * Lazy initialization of the EventManager dependecy.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager the EventManager dependency
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public function EventManager(\Brickoo\Event\Interfaces\Manager $EventManager = null);

        /**
         * Sets the requested Route for further routing.
         * @param \Brickoo\Routing\Interfaces\RequestRoute $RequestRoute the route matched the request
         * @throws \Brickoo\Core\Exceptions\ValueOverwriteException if trying to overwrite the request route
         * @return \Brickoo\Routing\Router
         */
        public function setRequestRoute(\Brickoo\Routing\Interfaces\RequestRoute $RequestRoute);

        /**
         * Checks if the requested Route has been found and is set.
         * @return boolean check result
         */
        public function hasRequestRoute();

        /**
         * Returns the request matching route.
         * If the Manager is available the proceded routes will be cached.
         * @throws Routing\Exceptions\RequestedHasNoRouteException if the request has not a matching Route
         * @return \Brickoo\Routing\Route
         */
        public function getRequestRoute();

        /**
         * Loads the Modules routes by asking over an event or collecting from filesystem.
         * @return \Brickoo\Routing\Router
         */
        public function loadModulesRoutes();

        /**
         * Saves the collected routes over an event notification.
         * @return \Brickoo\Routing\Router
         */
        public function saveModulesRoutes();

        /**
         * Collectes the routes available to add to the RouteCollection.
         * Searches through all available modules available to require the route collections.
         * This requires the registered modules, which is normaly done by the FrontController.
         * @return void
         */
        public function collectModulesRoutes();

    }