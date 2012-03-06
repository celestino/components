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

    namespace Brickoo\Cache;

    use Brickoo\Event,
        Brickoo\Validator\TypeValidator;

    /**
     * CacheListener
     *
     * Implements the event listeners for caching purposes.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheListener implements Event\Interfaces\ListenerAggregateInterface
    {

        /**
         * Holds an instance of the CacheManager class.
         * @var \Brickoo\Cache\Interfaces\CacheManagerInterface
         */
        protected $CacheManager;

        /**
         * Holds the listener priority.
         * @var integer
         */
        protected $listenerPriority;

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param \Brickoo\Cache\Interfaces\CacheManagerInterface $CacheManager the CacheManager to inject
         * @param integer $priority the listener priority
         * @return void
         */
        public function __construct(\Brickoo\Cache\Interfaces\CacheManagerInterface $CacheManager, $priority = 0)
        {
            TypeValidator::IsInteger($priority);

            $this->CacheManager        = $CacheManager;
            $this->listenerPriority    = $priority;
        }

        /**
         * Aggregates the event listeners.
         * @param \Brickoo\Event\Interfaces\EventManagerInterface $EventManager
         * @return void
         */
        public function aggregateListeners(\Brickoo\Event\Interfaces\EventManagerInterface $EventManager)
        {
            $EventManager->attachListener(CacheEvents::EVENT_CACHE_GET,
                array($this->CacheManager, 'get'), $this->listenerPriority, array('id')
            );
            $EventManager->attachListener(CacheEvents::EVENT_CACHE_CALLBACK,
                array($this->CacheManager, 'getByCallback'), $this->listenerPriority, array('id', 'callback', 'arguments', 'lifetime')
            );
            $EventManager->attachListener(CacheEvents::EVENT_CACHE_SET,
                array($this->CacheManager, 'get'), $this->listenerPriority, array('id', 'content', 'lifetime')
            );
            $EventManager->attachListener(CacheEvents::EVENT_CACHE_DELETE,
                array($this->CacheManager, 'get'), $this->listenerPriority, array('id')
            );
            $EventManager->attachListener(CacheEvents::EVENT_CACHE_FLUSH,
                array($this->CacheManager, 'get'), $this->listenerPriority
            );
        }

    }