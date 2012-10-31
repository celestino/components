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

    namespace Brickoo\Routing\Route\Interfaces;

    /**
     * ExecutableRoute
     *
     * Defines a route responsible for the request and can be executed.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface ExecutableRoute {

        /**
         * Returns the matched route.
         * @return \Brickoo\Routing\Interfaces\Route
         */
        public function getRoute();

        /**
         * Returns the value of the requested parameter.
         * @param string $parameter the parameter name
         * @throws \Brickoo\Routing\Route\Exceptions\ParameterNotAvailable if the parameter does not exist
         * @return string the parameter value
         */
        public function getParameter($parameter);

        /**
         * Checks if the parameter is available.
         * @param string $parameter the parameter name
         * @return boolean check result
         */
        public function hasParameter($parameter);

        /**
         * Executes the responsible controller action call with the route paramaters.
         * This method allows to be called with argument ot pass to the controller,
         * like for example an dependency container.
         * The ExecutableRoute is passed as reference to the action automaticly.
         * @return mixed the controller action returned response
         */
        public function execute();

    }