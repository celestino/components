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

    namespace Brickoo\Routing\Route;

    use Brickoo\Validator\Argument;

    /**
     * HttpRoute
     *
     * Implents a http route which can be configured to match http requests.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HttpRoute extends Route implements Interfaces\HttpRoute {

        /** @var string */
        private $method;

        /** @var string */
        private $scheme;

        /** @var string */
        private $hostname;

        /**
        * Class constructor.
        * @param string $name the unique route name.
        * @return void
        */
        public function __construct(
            $name, $path, $controller, $action,
            array $rules = array(), array $defaultValues = array(),
            $method = ".*", $scheme = ".*", $hostname = ".*"
        ){
            Argument::IsString($name);
            Argument::IsString($path);
            Argument::IsString($controller);
            Argument::IsString($action);
            Argument::IsString($method);
            Argument::IsString($scheme);

            $this->name = $name;
            $this->path = $path;
            $this->controller = $controller;
            $this->action = $action;
            $this->method = $method;
            $this->scheme = $scheme;
            $this->hostname = $hostname;
            $this->rules = $rules;
            $this->defaultValues = $defaultValues;
        }

        /** {@inheritDoc} */
        public function getMethod() {
            return $this->method;
        }

        /** {@inheritDoc} */
        public function getScheme() {
            return $this->scheme;
        }

        /** {@inheritDoc} */
        public function getHostname() {
            return $this->hostname;
        }

    }