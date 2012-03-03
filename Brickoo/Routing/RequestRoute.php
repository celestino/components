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

    namespace Brickoo\Routing;

    use Brickoo\Memory,
        Brickoo\Validator\TypeValidator;

    /**
     * RequestRoute
     *
     * Implements methods to handle the matched request route.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RequestRoute implements Interfaces\RequestRouteInterface
    {

        /**
         * Holds an instance of the matched request module route.
         * @var \Brickoo\Routing\Interfaces\RouteInterface
         */
        protected $ModuleRoute;

        /**
         * Returns the request module route.
         * @return \Brickoo\Routing\Interfaces\RouteInterface
         */
        public function getModuleRoute()
        {
            return $this->ModuleRoute;
        }

        /**
        * Holds the class dependencies.
        * @var array
        */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependecy the dependecy to inject
         * @return object RequestRoute if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependecy = null)
        {
            if ($Dependecy instanceof $interface) {
                $this->dependencies[$name] = $Dependecy;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the Container dependecy.
         * @param \Brickoo\Memory\Interfaces\ContainerInterface $Container the route parasm container
         * @return \Brickoo\Memory\Interfaces\ContainerInterface
         */
        public function Params(\Brickoo\Memory\Interfaces\ContainerInterface $Container = null)
        {
            return $this->getDependency(
                'Params',
                '\Brickoo\Memory\Interfaces\ContainerInterface',
                function(){return new Memory\Container();},
                $Container
            );
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param \Brickoo\Routing\Interfaces\RouteInterface $Routehte request module route
         * @return void
         */
        public function __construct(\Brickoo\Routing\Interfaces\RouteInterface $Route)
        {
            $this->ModuleRoute     = $Route;
            $this->dependencies    = array();
        }

        /**
         * Forwards the called method to the request module route.
         * @param string $method the method called
         * @param array $arguments the arguments passed
         * @throws \BadMethodCallException if the method does not exist
         * @return mixed the returned value of the called method
         */
        public function __call($method, $arguments)
        {
            if (! method_exists($this->ModuleRoute, $method)) {
                throw new \BadMethodCallException(sprintf('The Route method `%s` does not exist.', $method));
            }

            return call_user_func_array(array($this->ModuleRoute, $method), $arguments);
        }

    }