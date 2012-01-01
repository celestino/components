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

    namespace Brickoo\Library\Core;

    use Brickoo\Library\Core\Interfaces;
    use Brickoo\Library\Core\Exceptions;
    use Brickoo\Library\Routing;

    /**
     * Router
     *
     * Router class which handles the routes collected.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Router implements Interfaces\RouterInterface
    {

        /**
         * Holds the Request instance passed.
         * @var Brickoo\Library\Core\Interfaces\RequestInterface
         */
        protected $Request;

        /**
         * Holds the injected RouteCollecton instance.
         * @var Brickoo\Library\Routing\Interfaces\RouteCollectionInterface
         */
        protected $RouteCollection;

        /**
         * Lazy initialization of the RouteCollection dependecy.
         * Returns the injected ROuteCollection dependecy.
         * @return object RouteCollection implementing the RouteCollectionIterface
         */
        public function getRouteCollection()
        {
            if (! $this->RouteCollection instanceof Routing\Interfaces\RouteCollectionInterface)
            {
                $this->injectRouteCollecton(new Routing\RouteCollection());
            }

            return $this->RouteCollection;
        }

        /**
         * Injects the ROuteCollection dependency containign the assigned routes.
         * @param \Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection the colection of routes
         * @throws Exceptions\DependencyOverwriteException if the dependency is trying to overwrite
         * @return object reference
         */
        public function injectRouteCollection(\Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection)
        {
            if ($this->RouteCollection !== null)
            {
                throw new Exceptions\DependencyOverwriteException('RouteCollectionInterface');
            }

            $this->RouteCollection = $RouteCollection;

            return $this;
        }

        /**
         * Holds the requested Route instance.
         * @var Brickoo\Library\Routing\Interfaces\Route
         */
        protected $RequestedRoute;

        /**
         * Returns the request Route assigned.
         * @throws UnexpectedValueException if the request route is not set
         * @return object the Route matching the request
         */
        public function getRequestedRoute()
        {
            if ($this->RequestedRoute === null)
            {
                throw new UnexpectedValueException('The requeted Route is `null`.');
            }

            return $this->RequestedRoute;
        }

        /**
         * Sets the requested Route for further routing.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route matched the request
         * @throws Exceptions\DependencyOverwriteException if trying to overwrite the requested route
         * @return object reference
         */
        protected function setRequestedRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            if ($this->RequestedRoute !== null)
            {
                throw new Exceptions\DependencyOverwriteException('RouteInterface');
            }

            $this->RequestedRoute = $Route;

            return $this;
        }

        /**
         * Checks if the requested Route has been found and is set.
         * @return boolean check result
         */
        public function hasRequestedRoute()
        {
            return ($this->RequestedRoute instanceof Routing\Interfaces\RouteInterface);
        }


        public function isRequestedRoute(\Brickoo\Library\Routing\Interfacess\RouteInterface $Route)
        {
            // analyze the requet path with the passed route
        }

        /**
         * Returns
         * Enter description here ...
         * @throws \UnexpectedValueException
         * @throws Routing\Exceptions\RequestedHasNoRouteException
         * @return object RouteInterface the request route responsible
         */
        public function getRequestRoute()
        {
            if ($this->hasRequestedRoute())
            {
                return $this->RequestedRoute;
            }

            if (! ($Interator = $this->RouteCollection->getIterator()) instanceof \ArrayIterator)
            {
                throw new \UnexpectedValueException('The RouteCollection does not implement the ArrayIterator interface.');
            }

            $RequestedRoute = null;

            foreach($Collection as $Route)
            {
                if ($this->isRequestedRoute($Route))
                {
                    $this->setRequestedRoute($Route);
                    break;
                }
            }

            if (! $this->hasRequestedRoute())
            {
                throw new Routing\Exceptions\RequestedHasNoRouteException();
            }

            return $RequestedRoute;
        }

        /**
        * Class constructor.
        * Initializes the class properties.
        * @return void
        */
        public function __construct(\Brickoo\Library\Core\Interfaces\RequestInterface $Request)
        {
            $this->Request = $Request;

            $this->clear();
        }

        /**
        * Clears the object properties.
        * @return object reference
        */
        public function clear()
        {
            $this->RouteCollection    = null;

            return $this;
        }

    }

?>