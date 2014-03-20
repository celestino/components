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

namespace Brickoo\Component\Routing\Collector;

use ArrayIterator,
    Brickoo\Component\Messaging\GenericMessage,
    Brickoo\Component\Messaging\MessageDispatcher,
    Brickoo\Component\Messaging\MessageResponseCollection,
    Brickoo\Component\Routing\Messages,
    Brickoo\Component\Routing\RouteCollection,
    Brickoo\Component\Routing\RouteCollector;


/**
 * MessageRouteCollector
 *
 * Implementation of a route collection based on messaging collection call.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageRouteCollector implements RouteCollector {

    /** @var \Brickoo\Component\Messaging\MessageDispatcher */
    private $messageDispatcher;

    /** @var array */
    private $collections;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Messaging\MessageDispatcher $messageDispatcher
     */
    public function __construct(MessageDispatcher $messageDispatcher) {
        $this->messageDispatcher = $messageDispatcher;
        $this->collections = [];
    }

    /** {@inheritDoc} */
    public function collect() {
        $message = new GenericMessage(Messages::COLLECT_ROUTES, $this);
        $this->messageDispatcher->dispatch($message);
        $this->collections = $this->extractRouteCollections($message->getResponse());
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
     * Extracts collected route collections from the message response.
     * @param \Brickoo\Component\Messaging\MessageResponseCollection $messageResponseCollection
     * @return array the extracte collections
     */
    private function extractRouteCollections(MessageResponseCollection $messageResponseCollection) {
        $collections = [];
        while (! $messageResponseCollection->isEmpty()) {
            if (($routeCollection = $messageResponseCollection->shift()) instanceof RouteCollection) {
                $collections[] = $routeCollection;
            }
        }
        return $collections;
    }

}