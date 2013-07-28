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

    namespace Brickoo\Cache;

    use Brickoo\Event,
        Brickoo\Event\Listener as EventListener,
        Brickoo\Validator\Argument;

    /**
     * Listener
     *
     * Implements the attachment of cache listeners to an event manager.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Listener implements Event\Interfaces\ListenerAggregate {

        /**
         * Listener event parameters.
         * @var string
         */
        const PARAM_IDENTIFIER = "id";
        const PARAM_CONTENT = "content";
        const PARAM_CALLBACK = "callback";
        const PARAM_CALLBACK_ARGS = "callbackArguments";
        const PARAM_LIFETIME = "lifetime";

        /** @var \Brickoo\Cache\Interfaces\Manager */
        private $CacheManager;

        /** @var integer */
        private $listenerPriority;

        /**
         * Class constructor.
         * @param \Brickoo\Cache\Interfaces\Manager $Manager the Manager to inject
         * @param integer $priority the listener priority
         * @return void
         */
        public function __construct(\Brickoo\Cache\Interfaces\Manager $CacheManager, $priority = 0) {
            Argument::IsInteger($priority);

            $this->CacheManager        = $CacheManager;
            $this->listenerPriority    = $priority;
        }

        /** {@inheritDoc} */
        public function attachListeners(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $EventManager->attach(new EventListener(
                Events::GET,
                array($this, "handleCacheEventGet"),
                $this->listenerPriority
            ));
            $EventManager->attach(new EventListener(
                Events::CALLBACK,
                array($this, "handleCacheEventGetByCallback"),
                $this->listenerPriority
            ));
            $EventManager->attach(new EventListener(
                Events::SET,
                array($this, "handleCacheEventSet"),
                $this->listenerPriority
            ));
            $EventManager->attach(new EventListener(
                Events::DELETE,
                array($this, "handleCacheEventDelete"),
                $this->listenerPriority
            ));
            $EventManager->attach(new EventListener(
                Events::FLUSH,
                array($this, "handleCacheEventFlush"),
                $this->listenerPriority
            ));
        }

        /**
         * Handle the event to retrieve the cached content from the injected cache manager.
         * @param \Brickoo\Event\Interfaces\Event $Event
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return mixed the event response otherwise null on failure
         */
        public function handleCacheEventGet(\Brickoo\Event\Interfaces\Event $Event, \Brickoo\Event\Interfaces\Manager $EventManager) {
            if ($Event->hasParam(self::PARAM_IDENTIFIER)) {
                return $this->CacheManager->get($Event->getParam(self::PARAM_IDENTIFIER));
            }
        }

        /**
         * Handle the event to retrieve the cached content from the injected cache manager
         * with a callback used as a fallback.
         * @param \Brickoo\Event\Interfaces\Event $Event
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return mixed the event response otherwise null on failure
         */
        public function handleCacheEventGetByCallback(\Brickoo\Event\Interfaces\Event $Event, \Brickoo\Event\Interfaces\Manager $EventManager) {
            if ($Event->hasParams(self::PARAM_IDENTIFIER, self::PARAM_CALLBACK, self::PARAM_CALLBACK_ARGS, self::PARAM_LIFETIME)) {
                return $this->CacheManager->getByCallback(
                    $Event->getParam(self::PARAM_IDENTIFIER),
                    $Event->getParam(self::PARAM_CALLBACK),
                    $Event->getParam(self::PARAM_CALLBACK_ARGS),
                    $Event->getParam(self::PARAM_LIFETIME)
                );
            }
        }

        /**
         * Handle the event to store content through the injected cache manager.
         * @param \Brickoo\Event\Interfaces\Event $Event
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return void
         */
        public function handleCacheEventSet(\Brickoo\Event\Interfaces\Event $Event, \Brickoo\Event\Interfaces\Manager $EventManager) {
            if ($Event->hasParams(self::PARAM_IDENTIFIER, self::PARAM_CONTENT, self::PARAM_LIFETIME)) {
                $this->CacheManager->set(
                    $Event->getParam(self::PARAM_IDENTIFIER),
                    $Event->getParam(self::PARAM_CONTENT),
                    $Event->getParam(self::PARAM_LIFETIME)
                );
            }
        }

        /**
         * Handle the event to delete the cached content holded by the identifier
         * through the injected cache manager.
         * @param \Brickoo\Event\Interfaces\Event $Event
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         */
        public function handleCacheEventDelete(\Brickoo\Event\Interfaces\Event $Event, \Brickoo\Event\Interfaces\Manager $EventManager) {
            if ($Event->hasParam(self::PARAM_IDENTIFIER)) {
                $this->CacheManager->delete($Event->getParam(self::PARAM_IDENTIFIER));
            }
        }

        /**
         * Handle to flush the cache content through the injected cache manager.
         * @param \Brickoo\Event\Interfaces\Event $Event
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         */
        public function handleCacheEventFlush(\Brickoo\Event\Interfaces\Event $Event, \Brickoo\Event\Interfaces\Manager $EventManager) {
            $this->CacheManager->flush();
        }

    }