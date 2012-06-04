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
         * Sets the modules to load the routes the routing information.
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
         * Loads the Modules routes.
         * @return \Brickoo\Routing\Router
         */
        public function loadModulesRoutes();

        /**
         * Saves the collected routes.
         * @return \Brickoo\Routing\Router
         */
        public function saveModulesRoutes();

    }