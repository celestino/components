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

    namespace Brickoo\Routing\Interfaces;

    /**
     * Router
     *
     * Defines a router to determine the request responsible route to execute.
     * Also the router should return any available route.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface Router {

        /**
         * Returns the route having the given unique name.
         * @param string $routeName the route unique name
         * @param string $collectionName the route collections name
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Routing\Route\Exceptions\RouteNotFound if the route is not available
         * @return \Brickoo\Routing\Interfaces\Route
         */
        public function getRoute($routeName, $collectionName = null);

        /**
         * Checks if the route is available.
         * @param string $routeName the route unique name
         * @param string $collectionName the route collections name
         * @throws \InvalidArgumentException if the argument is not valid
         * @return boolean check result
         */
        public function hasRoute($routeName, $collectionName = null);

        /**
         * Returns the request matching executable route.
         * @return \Brickoo\Routing\Interfaces\RequestRoute
         * @throws \Brickoo\Routing\Exceptions\RequestHasNoRoute if non route for the request is available
         * @return \Brickoo\Routing\Route\Interfaces\Executable
         */
        public function getExecutable();

    }