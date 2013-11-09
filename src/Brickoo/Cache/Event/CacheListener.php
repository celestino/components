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

namespace Brickoo\Cache\Event;

use Brickoo\Cache\CacheManager,
    Brickoo\Event\Event,
    Brickoo\Event\EventDispatcher,
    Brickoo\Event\GenericListener,
    Brickoo\Event\ListenerAggregate,
    Brickoo\Validator\Argument;

/**
 * CacheManagerListener
 *
 * Implements the attachment of cache listeners to an event dispatcher
 * having a cache manager as dependency for event processing cache operations.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class CacheManagerListener implements ListenerAggregate {

    /** @var \Brickoo\Cache\CacheManager */
    private $cacheManager;

    /** @var integer */
    private $listenerPriority;

    /**
     * Class constructor.
     * @param \Brickoo\Cache\CacheManager $cacheManager
     * @param integer $priority the listener priority
     * @return void
     */
    public function __construct(CacheManager $cacheManager, $priority = 0) {
        Argument::IsInteger($priority);

        $this->cacheManager = $cacheManager;
        $this->listenerPriority = $priority;
    }

    /** {@inheritDoc} */
    public function attachListeners(EventDispatcher $dispatcher) {
        $dispatcher->attach(new GenericListener(
            Events::GET,
            $this->listenerPriority,
            [$this, "handleRetrieveEvent"],
            [$this, "isEventSupported"]
        ));
        $dispatcher->attach(new GenericListener(
            Events::CALLBACK,
            $this->listenerPriority,
            [$this, "handleRetrieveByCallbackEvent"],
            [$this, "isEventSupported"]
        ));
        $dispatcher->attach(new GenericListener(
            Events::SET,
            $this->listenerPriority,
            [$this, "handleStoreEvent"],
            [$this, "isEventSupported"]
        ));
        $dispatcher->attach(new GenericListener(
            Events::DELETE,
            $this->listenerPriority,
            [$this, "handleDeleteEvent"],
            [$this, "isEventSupported"]
        ));
        $dispatcher->attach(new GenericListener(
            Events::FLUSH,
            $this->listenerPriority,
            [$this, "handleFlushEvent"],
            [$this, "isEventSupported"]
        ));
    }

    /**
     * Checks if the event is supported.
     * @param \Brickoo\Event\Event $event
     * @param \Brickoo\Event\EventDispatcher $eventDispatcher
     * @return boolean check result
     */
    public function isEventSupported(Event $event, EventDispatcher $eventDispatcher) {
        return  ($event instanceof AbstractEvent);
    }

    /**
     * Handle the event to retrieve the cached content from the injected cache manager.
     * @param \Brickoo\Cache\Event\RetrieveEvent $Event
     * @param \Brickoo\Event\EventDispatcher $Dispatcher
     * @return mixed the cached content
     */
    public function handleRetrieveEvent(RetrieveEvent $Event, EventDispatcher $Dispatcher) {
        return $this->cacheManager->get($Event->getIdentifier());
    }

    /**
     * Handle the event to retrieve the cached content from the injected cache manager
     * with a callback used as a fallback.
     * @param \Brickoo\Cache\Event\RetrieveByCallbackEvent $Event
     * @param \Brickoo\Event\EventDispatcher $Dispatcher
     * @return mixed the cached content
     */
    public function handleRetrieveByCallbackEvent(RetrieveByCallbackEvent $Event, EventDispatcher $Dispatcher) {
        return $this->cacheManager->getByCallback(
            $Event->getIdentifier(),
            $Event->getCallback(),
            $Event->getCallbackArguments(),
            $Event->getLifetime()
        );
    }

    /**
     * Handle the event to cache content.
     * @param \Brickoo\Cache\Event\StoreEvent $Event
     * @param \Brickoo\Event\EventDispatcher $Dispatcher
     * @return void
     */
    public function handleStoreEvent(StoreEvent $Event, EventDispatcher $Dispatcher) {
        $this->cacheManager->set($Event->getIdentifier(), $Event->getContent(), $Event->getLifetime());
    }

    /**
     * Handle the event to delete the cached content holded by the identifier
     * through the injected cache manager.
     * @param \Brickoo\Cache\Event\DeleteEvent $Event
     * @param \Brickoo\Event\EventDispatcher $Dispatcher
     * @return void
     */
    public function handleCacheEventDelete(DeleteEvent $Event, EventDispatcher $Dispatcher) {
        $this->cacheManager->delete($Event->getIdentifier());
    }

    /**
     * Handle to flush the cache content through the injected cache manager.
     * @param \Brickoo\Cache\Event\FlushEvent $Event
     * @param \Brickoo\Event\EventDispatcher $Dipatcher
     * @return void
     */
    public function handleCacheEventFlush(FlushEvent $Event, EventDispatcher $Dispatcher) {
        $this->cacheManager->flush();
    }

}