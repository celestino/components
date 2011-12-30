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

    use Brickoo\Library\Routing\Interfaces;
    use Brickoo\Library\Routing\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Route
     *
     * Implents a Route which can be configured to handle requests
     * which execute the assigned controller and action.<ss
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id $
     */

    class Route implements Interfaces\RouteInterface
    {

        /**
         * Holds the route path to listen to.
         * @var string
         */
        protected $path;

        /**
         * Returns the route path listening.
         * @throws UnexpectedValueException if the path is null
         * @return string the route path listening
         */
        public function getPath()
        {
            if ($this->path === null)
            {
                throw new UnexpectedValueException('The route path is ´null´.');
            }

            return $this->path;
        }

        /**
         * Sets the route path to listen to.
         * @param string $path the path to liste to
         * @return object reference
         */
        public function setPath($path)
        {
            TypeValidator::Validate('isString', array($path));

            $this->path = $path;

            return $this;
        }

        /**
         * Holds the controller which should be executed.
         * @var string
         */
        protected $controller;

        /**
         * Returns the controller to execute.
         * @throws UnexpectedValueException if the controller is null
         * @return string the controller to execute
         */
        public function getController()
        {
            if ($this->controller === null)
            {
                throw new UnexpectedValueException('The route controller is ´null´.');
            }

            return $this->controller;
        }

        /**
         * Sets the controller to execute.
         * @param string $controller the controller to execute
         * @return object reference
         */
        public function setController($controller)
        {
            TypeValidator::Validate('isString', array($controller));

            $this->controller = $controller;

            return $this;
        }

        /**
         * Holds the method within the controller to call.
         * @var string
         */
        protected $method;

        /**
         * Returns the metthod to call.
         * @throws UnexpectedValueException if the method is null
         * @return string the method to call
         */
        public function getMethod()
        {
            if ($this->method === null)
            {
                throw new UnexpectedValueException('The route method is ´nul´.');
            }

            return $this->method;
        }

    }

?>