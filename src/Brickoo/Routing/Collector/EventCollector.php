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

    namespace Brickoo\Routing\Collector;

    use Brickoo\Event\Event,
        Brickoo\Routing\Route\Collection,
        Brickoo\Routing\Events;

    /**
     * EventCollector
     *
     * Implementation of a route search based on event collection calls.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventCollector implements Interfaces\Collector {

        /** @var \Brickoo\Event\Interfaces\Manager */
        private $EventManager;

        /**
         * Class constructor.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @param \Brickoo\Routing\Route\Interfaces\Collection $RouteCollection
         * @return void
         */
        public function __construct(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $this->EventManager = $EventManager;
        }

        /** {@inheritDoc} */
        public function collect() {
            $EventResponseCollection = $this->EventManager->collect(new Event(Events::SEARCH, $this));

            if ($EventResponseCollection->isEmpty()) {
                throw new Exceptions\RoutesNotAvailable();
            }

            return $this->getRouteCollection($EventResponseCollection);
        }

        /**
         * Returns one route collection containing all collected routes.
         * @param \Brickoo\Event\Response\Interfaces\Collection $EventResponseCollection
         * @throws \Brickoo\Routing\Collector\Exceptions\RouteCollectionExpected
         * @return \Brickoo\Routing\Route\Interfaces\Collection
         */
        private function getRouteCollection(\Brickoo\Event\Response\Interfaces\Collection $EventResponseCollection) {
            if (count($EventResponseCollection) == 1) {
                if (! ($RouteCollection = $EventResponseCollection->shift()) instanceof \Brickoo\Routing\Route\Interfaces\Collection) {
                    throw new Exceptions\RouteCollectionExpected($RouteCollection);
                }
                return $RouteCollection;
            }

            return $this->getMergedRouteCollection($EventResponseCollection);
        }

        /**
         * Merges collections to one collection containing all routes.
         * @param \Brickoo\Event\Response\Interfaces\Collection $EventResponseCollection
         * @throws \Brickoo\Routing\Collector\Exceptions\RouteCollectionExpected
         * @return \Brickoo\Routing\Route\Interfaces\Collection
         */
        private function getMergedRouteCollection(\Brickoo\Event\Response\Interfaces\Collection $EventResponseCollection) {
            $MergedRouteCollection = new Collection();

            while (! $EventResponseCollection->isEmpty()) {
                if (! ($RouteCollection = $EventResponseCollection->shift()) instanceof \Brickoo\Routing\Route\Interfaces\Collection) {
                    throw new Exceptions\RouteCollectionExpected($RouteCollection);
                }
                $MergedRouteCollection->addRoutes($RouteCollection->getRoutes());
            }

            return $MergedRouteCollection;
        }

    }