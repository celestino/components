<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Component\Routing\Route\Collector;

use Brickoo\Component\Common\ArrayList;
use Brickoo\Component\Common\Collection;
use Brickoo\Component\Messaging\GenericMessage;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Routing\Messaging\Messages;
use Brickoo\Component\Routing\Route\RouteCollection;

/**
 * MessageRouteCollector
 *
 * Implementation of a route collection based on messaging collection call.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class MessageRouteCollector implements RouteCollector {

    /** @var \Brickoo\Component\Messaging\MessageDispatcher */
    private $messageDispatcher;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Messaging\MessageDispatcher $messageDispatcher
     */
    public function __construct(MessageDispatcher $messageDispatcher) {
        $this->messageDispatcher = $messageDispatcher;
    }

    /** {@inheritDoc} */
    public function collect() {
        $message = new GenericMessage(Messages::COLLECT_ROUTES, $this);
        $this->messageDispatcher->dispatch($message);
        return $this->extractRouteCollections($message->getResponseList());
    }

    /**
     * Extracts collected route collections from the message response.
     * @param \Brickoo\Component\Common\ArrayList $messageResponseList
     * @return array
     */
    private function extractRouteCollections(ArrayList $messageResponseList) {
        $collection = new Collection();
        foreach ($messageResponseList as $item) {
            if ($item instanceof RouteCollection) {
                $collection->add($item);
            }
        }
        return $collection;
    }

}
