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

    namespace Brickoo\Event\Builder;

    use Brickoo\Validator\Argument,
        Brickoo\Validator\Constraint,
        Brickoo\Event\Manager,
        Brickoo\Event\Process\Processor,
        Brickoo\Event\Listener\Collection,
        Brickoo\Memory\Container;

    /**
     * EventManagerBuilder
     *
     * Implementation of an event manager builder.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class EventManagerBuilder implements Interfaces\EventManagerBuilder {

        /** @var \Brickoo\Event\Process\Interfaces\Processor */
        private $Processor;

        /** @var \Brickoo\Event\Listener\Collection */
        private $ListenerCollection;

        /** @var \Brickoo\Memory\Interfaces\Container */
        private $EventList;

        /** @var \Traversable|array */
        private $listeners;

        /**
         * Class constructor.
         * @return void
         */
        public function __construct() {
            $this->listeners = array();
        }

        /** {@inheritDoc} */
        public function setEventProcessor(\Brickoo\Event\Process\Interfaces\Processor $Processor) {
            $this->Processor = $Processor;
            return $this;
        }

        /** {@inheritDoc} */
        public function setListenerCollection(\Brickoo\Event\Listener\Interfaces\Collection $ListenerCollection) {
            $this->ListenerCollection = $ListenerCollection;
            return $this;
        }

        /** {@inheritDoc} */
        public function setEventList(\Brickoo\Memory\Interfaces\Container $EventList) {
            $this->EventList = $EventList;
            return $this;
        }

        /** {@inheritDoc} */
        public function setListeners($listeners) {
            Argument::IsTraversable($listeners);

            $Constraint = new Constraint\TraversableContainsInstancesOf('Brickoo\Event\Interfaces\Listener');
            if (! $Constraint->assert($listeners)) {
                throw new \InvalidArgumentException("The traversable must contain Event\Listeners only.");
            }

            $this->listeners = $listeners;
            return $this;
        }

        /** {@inheritDoc} */
        public function build() {
            $EventManager = new Manager(
                $this->getEventProcessor(), $this->getListenerCollection(), $this->getEventList()
            );
            $this->attachListeners($EventManager);
            return $EventManager;
        }

        /**
         * Returns the configured event processor dependency.
         * If it does not exists it will be created using the framework implementation.
         * @return \Brickoo\Event\Process\Interfaces\Processor
         */
        private function getEventProcessor() {
            if ($this->Processor === null) {
                $this->Processor = new Processor();
            }

            return $this->Processor;
        }

        /**
         * Returns the configured listener collection dependency.
         * If it does not exists it will be created using the framework implementation.
         * @return \Brickoo\Event\Listener\Collection
         */
        private function getListenerCollection() {
            if ($this->ListenerCollection === null) {
                $this->ListenerCollection = new Collection();
            }

            return $this->ListenerCollection;
        }

        /**
         * Returns the configured memory event list dependency.
         * If it does not exists it will be created using the framework implementation.
         * @return Ambigous <\Brickoo\Memory\Interfaces\Container, \Brickoo\Memory\Container>
         */
        private function getEventList() {
            if ($this->EventList === null) {
                $this->EventList = new Container();
            }

            return $this->EventList;
        }

        /**
         * Attach the configured event listeners to the event manager.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return void
         */
        private function attachListeners(\Brickoo\Event\Interfaces\Manager $EventManager) {
            foreach ($this->listeners as $Listener) {
                $EventManager->attach($Listener);
            }
        }

    }