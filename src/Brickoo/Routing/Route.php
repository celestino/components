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

    namespace Brickoo\Routing;

    use Brickoo\Validator\Argument;

    /**
     * Route
     *
     * Implents a Route which can be configured to match requests
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Route implements Interfaces\Route {

        /** @var string */
        private $name;

        /** @var string */
        private $path;

        /** @var string */
        private $controller;

        /** @var string */
        private $action;

        /** @var string */
        private $method;

        /** @var string */
        private $scheme;

        /** @var string */
        private $hostname;

        /** @var array */
        private $defaultValues;

        /** @var array */
        private $rules;

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
        public function getName() {
            return $this->name;
        }


        /** {@inheritDoc} */
        public function getPath() {
            return $this->path;
        }

        /** {@inheritDoc} */
        public function getController() {
            return $this->controller;
        }

        /** {@inheritDoc} */
        public function getAction() {
            return $this->action;
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

        /** {@inheritDoc} */
        public function getRules() {
            return $this->rules;
        }

        /** {@inheritDoc} */
        public function getRule($parameter) {
            Argument::IsString($parameter);

            if (! $this->hasRule($parameter)) {
                throw new \UnexpectedValueException(
                    sprintf("The rule for `%s` does not exist.", $parameter)
                );
            }

            return $this->rules[$parameter];
        }

        /** {@inheritDoc} */
        public function hasRules() {
            return (! empty($this->rules));
        }

        /** {@inheritDoc} */
        public function hasRule($parameter) {
            Argument::IsString($parameter);

            return array_key_exists($parameter, $this->rules);
        }

        /** {@inheritDoc} */
        public function getDefaultValues() {
            return $this->defaultValues;
        }

        /** {@inheritDoc} */
        public function getDefaultValue($parameter) {
            Argument::IsString($parameter);

            if (!$this->hasDefaultValue($parameter)) {
                throw new \UnexpectedValueException(
                    sprintf("The default vaule for the parameter `%s` does not exist.", $parameter)
                );
            }
            return $this->defaultValues[$parameter];
        }

        /** {@inheritDoc} */
        public function hasDefaultValue($parameter) {
            Argument::IsString($parameter);

            return array_key_exists($parameter, $this->defaultValues);
        }

    }