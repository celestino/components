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

    namespace Brickoo\Core;

    /**
     * Controller
     *
     * Implements methods to provide access to the basic components
     * to avoid the performace cost of calling the Registry for often used components.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Controller implements Interfaces\ControllerInterface
    {

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
         * @return object Controller if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependecy = null)
        {
            if ($Dependecy instanceof $interface) {
                $this->dependencies[$name] = $Dependecy;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                return $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Returns an instance of the Registry class.
         * @param \Brickoo\Core\Interfaces\RegistryInterface $Registry the Registry dependency to inject
         * @return \Brickoo\Core\Interfaces\RegistryInterface
         */
        public function Registry(\Brickoo\Core\Interfaces\RegistryInterface $Registry = null)
        {
            return $this->getDependency(
                'Registry',
                '\Brickoo\Core\Interfaces\RegistryInterface',
                function() {throw new Exceptions\DependencyNotAvailableException('RegistryInterface');},
                $Registry
            );
        }

        /**
         * Returns an instance of the Application class.
         * @param \Brickoo\Core\Application $Application the Application dependency to inject
         * @return \Brickoo\Core\Application
         */
        public function Application(\Brickoo\Core\Application $Application = null)
        {
            return $this->getDependency(
                'Application',
                '\Brickoo\Core\Application',
                function() {throw new Exceptions\DependencyNotAvailableException('Application');},
                $Application
            );
        }

        /**
         * Returns an instance of the Request class.
         * @param \Brickoo\Core\Interfaces\RequestInterface $Request the Request dependency to inject
         * @return \Brickoo\Core\Interfaces\RequestInterface
         */
        public function Request(\Brickoo\Core\Interfaces\RequestInterface $Request = null)
        {
            return $this->getDependency(
                'Request',
                '\Brickoo\Core\Interfaces\RequestInterface',
                function() {throw new Exceptions\DependencyNotAvailableException('RequestInterface');},
                $Request
            );
        }

        /**
         * Returns an instance of the RequestRoute class.
         * @param \Brickoo\Routing\Interfaces\RouteInterface $Route the Route dependency to inject
         * @return \Brickoo\Routing\Interfaces\RouteInterface
         */
        public function Route(\Brickoo\Routing\Interfaces\RequestRouteInterface $Route = null)
        {
            return $this->getDependency(
                'Route',
                '\Brickoo\Routing\Interfaces\RequestRouteInterface',
                function() {throw new Exceptions\DependencyNotAvailableException('RequestRouteInterface');},
                $Route
            );
        }

    }