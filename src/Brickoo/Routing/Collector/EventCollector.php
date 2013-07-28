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

    namespace Brickoo\Routing\Collector;

    use Brickoo\Event\Event,
        Brickoo\Routing\Events;

    /**
     * EventCollector
     *
     * Implementation of a route collection based on event collection calls.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventCollector implements Interfaces\Collector {

        /** @var \Brickoo\Event\Interfaces\Manager */
        private $EventManager;

        /** @var array */
        private $collections;

        /**
         * Class constructor.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return void
         */
        public function __construct(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $this->EventManager = $EventManager;
            $this->collections = array();
        }

        /** {@inheritDoc} */
        public function collect() {
            $EventResponseCollection = $this->EventManager->collect(new Event(Events::COLLECT_ROUTES, $this));

            if ($EventResponseCollection->isEmpty()) {
                throw new Exceptions\RoutesNotAvailable();
            }

            $this->extractRouteCollections($EventResponseCollection);
            if (empty($this->collections)) {
                throw new Exceptions\RoutesNotAvailable();
            }

            return $this;
        }

        /**
         * {@inheritDoc}
         * @see IteratorAggregate::getIterator()
         * @return \ArrayIterator containing the route collections
         */
        public function getIterator() {
            return new \ArrayIterator($this->collections);
        }

        /**
         * Extracts collected route collections from the event response.
         * @param \Brickoo\Event\Response\Interfaces\Collection $EventResponseCollection
         * @return void
         */
        private function extractRouteCollections(\Brickoo\Event\Response\Interfaces\Collection $EventResponseCollection) {
            while (! $EventResponseCollection->isEmpty()) {
                if (($RouteCollection = $EventResponseCollection->shift()) instanceof \Brickoo\Routing\Route\Interfaces\Collection) {
                    $this->collections[] = $RouteCollection;
                }
            }
        }

    }