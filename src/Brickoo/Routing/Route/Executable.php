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

    namespace Brickoo\Routing\Route;

    use Brickoo\Http\Session\Handler\CacheHandler;

    use Brickoo\Validator\Argument;

    /**
     * Executable
     *
     * Implementation of a request responsible route which can be executed.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Executable implements Interfaces\Executable {

        /** @var \Brickoo\Routing\Interfaces\Route */
        private $Route;

        /** @var array */
        private $parameters;

        /** @var boolean */
        private $hasBeenExecuted;

        /**
         * Class constructor.
         * @param \Brickoo\Routing\Interfaces\Route $Route the matching request route
         * @param array $parameters the paramaters extracted from the route
         * @return void
         */
        public function __construct(\Brickoo\Routing\Interfaces\Route $Route, array $parameters = array()) {
            $this->Route = $Route;
            $this->parameters = $parameters;
            $this->hasBeenExecuted = false;
        }

        /** {@inheritDoc} */
        public function getRoute() {
            return $this->Route;
        }

        /** {@inheritDoc} */
        public function getParameters() {
            return $this->parameters;
        }

        /** {@inheritDoc} */
        public function getParameter($parameter) {
            Argument::IsString($parameter);

            if (! $this->hasParameter($parameter)) {
                throw new Exceptions\ParameterNotAvailable($parameter);
            }

            return $this->parameters[$parameter];
        }

        /** {@inheritDoc} */
        public function hasParameter($parameter) {
            Argument::IsString($parameter);
            return array_key_exists($parameter, $this->parameters);
        }

        /** {@inheritDoc} */
        public function execute() {
            if ($this->hasBeenExecuted) {
                throw new Exceptions\MultipleExecutions($this->getRoute()->getName());
            }

            if (func_num_args() == 0 || (! $parameter = func_get_arg(0))) {
                $parameter = null;
            }

            $this->hasBeenExecuted = true;

            $Controller = $this->Route->getController();
            $actionMethod = $this->Route->getAction();
            $Controller = new $Controller($parameter);
            return $Controller->{$actionMethod}();
        }

    }