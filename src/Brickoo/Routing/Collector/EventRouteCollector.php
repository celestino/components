<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

use ArrayIterator,
    Brickoo\Event\GenericEvent,
    Brickoo\Event\EventDispatcher,
    Brickoo\Event\ResponseCollection,
    Brickoo\Routing\Events,
    Brickoo\Routing\RouteCollection,
    Brickoo\Routing\RouteCollector;


/**
 * EventRouteCollector
 *
 * Implementation of a route collection based on event collection call.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventRouteCollector implements RouteCollector {

    /** @var \Brickoo\Event\Interfaces\Manager */
    private $eventDispatcher;

    /** @var array */
    private $collections;

    /**
     * Class constructor.
     * @param \Brickoo\Event\EventDispatcher $eventDispatcher
     * @return void
     */
    public function __construct(EventDispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
        $this->collections = [];
    }

    /** {@inheritDoc} */
    public function collect() {
        $this->collections = $this->extractRouteCollections(
            $this->eventDispatcher->collect(new GenericEvent(Events::COLLECT_ROUTES, $this))
        );
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     * @return \ArrayIterator containing the route collections
     */
    public function getIterator() {
        return new ArrayIterator($this->collections);
    }

    /**
     * Extracts collected route collections from the event response.
     * @param \Brickoo\Event\ResponseCollection $eventResponseCollection
     * @return array the extracte collections
     */
    private function extractRouteCollections(ResponseCollection $eventResponseCollection) {
        $collections = [];
        while (! $eventResponseCollection->isEmpty()) {
            if (($RouteCollection = $eventResponseCollection->shift()) instanceof RouteCollection) {
                $collections[] = $RouteCollection;
            }
        }
        return $collections;
    }

}