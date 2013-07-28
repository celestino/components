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

    namespace Brickoo\Event\Builder\Interfaces;

    /**
     * EventManagerBuilder
     *
     * Describes an event manager builder.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface EventManagerBuilder {

        /**
         * Sets the event processor dependency.
         * @param \Brickoo\Event\Process\Interfaces\Processor $Processor
         * @return \Brickoo\Event\Builder\Interfaces\EventManager
         */
        public function setEventProcessor(\Brickoo\Event\Process\Interfaces\Processor $Processor);

        /**
         * Sets the listener collection dependency.
         * @param \Brickoo\Event\Listener\Collection $ListenerCollection
         * @return \Brickoo\Event\Builder\Interfaces\EventManager
         */
        public function setListenerCollection(\Brickoo\Event\Listener\Interfaces\Collection $ListenerCollection);

        /**
         * Sets the event memory list dependency.
         * @param \Brickoo\Memory\Interfaces\Container $EventList
         * @return \Brickoo\Event\Builder\Interfaces\EventManager
         */
        public function setEventList(\Brickoo\Memory\Interfaces\Container $EventList);

        /**
         * Sets the event listeners of the event manager.
         * @param \Traversable|array $listeners
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Event\Builder\Interfaces\EventManager
         */
        public function setListeners($listeners);

        /**
         * Builds the event manager based on the configuration or
         * default implementations.
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public function build();

    }