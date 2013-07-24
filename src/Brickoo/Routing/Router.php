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
     * Router
     *
     * Router which can return the request matching executable route
     * and any route available based on the unique name
     * For collecting the availables routes a collector is used
     * which does provide the available routes as collection.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Router implements Interfaces\Router {

        /**  @var \Brickoo\Routing\Interfaces\Executable */
        private $Executable;

        /** @var \Brickoo\Routing\Matcher\Interfaces\Matcher */
        private $Matcher;

        /** @var \Brickoo\Routing\Collector\Interfaces\Collector */
        private $Collector;

        /** @var \ArrayIterator */
        private $CollectorIterator;

        /**
        * Class constructor.
        * @param \Brickoo\Routing\Route\Interfaces\Collector $Collector
        * @param \Brickoo\Routing\Route\Interfaces\Matcher $Matcher
        * @return void
        */
        public function __construct(
            \Brickoo\Routing\Collector\Interfaces\Collector $Collector,
            \Brickoo\Routing\Matcher\Interfaces\Matcher $Matcher
        ){
            $this->Collector = $Collector;
            $this->Matcher = $Matcher;
        }

        /** {@inheritDoc} */
        public function getRoute($routeName, $collectionName = null) {
            if ($collectionName !== null) {
                Argument::IsString($collectionName);
            }
            Argument::IsString($routeName);

            $Route = null;
            foreach ($this->getCollectorIterator() as $RouteCollection) {
                if (($collectionName === null || ($RouteCollection->hasName() && $RouteCollection->getName() == $collectionName))
                    && $RouteCollection->hasRoute($routeName)
                ){
                    $Route = $RouteCollection->getRoute($routeName);
                    break;
                }
            }

            if ($Route === null) {
                throw new Route\Exceptions\RouteNotFound($routeName);
            }

            return $Route;
        }

        /** {@inheritDoc} */
        public function hasRoute($routeName, $collectionName = null) {
            if ($collectionName !== null) {
                Argument::IsString($collectionName);
            }
            Argument::IsString($routeName);

            try {
                $Route = $this->getRoute($routeName, $collectionName);
            }
            catch (Route\Exceptions\RouteNotFound $Exception) {
                return false;
            }

            return ($Route instanceof \Brickoo\Routing\Route\Interfaces\Route);
        }

        /** {@inheritDoc} */
        public function getExecutable() {
            if ($this->Executable === null) {
                foreach ($this->getCollectorIterator() as $RouteCollection) {
                    if (! $this->Matcher->matchesCollection($RouteCollection)) {
                        continue;
                    }

                    foreach ($RouteCollection->getRoutes() as $Route) {
                        if ($this->Matcher->matchesRoute($Route)) {
                            $this->Executable = new Route\Executable($Route, $this->Matcher->getRouteParameters());
                            break;
                        }
                    }
                }

                if ($this->Executable === null) {
                    throw new Exceptions\NoMatchingRouteFound();
                }
            }

            return $this->Executable;
        }

        /**
         * Returns an iterator from collector containing the route collections.
         * @return \ArrayIterator the route collections iterator
         */
        private function getCollectorIterator() {
            if ($this->CollectorIterator === null) {
                $this->CollectorIterator = $this->Collector->collect()->getIterator();
            }

            return $this->CollectorIterator;
        }

    }