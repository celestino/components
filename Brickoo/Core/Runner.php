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

    namespace Brickoo\Core;

    use Brickoo\Core\Events,
        Brickoo\Event\Event;

    /**
     * Runner
     *
     * Runs the application basic logic.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Runner implements \Brickoo\Core\Interfaces\Runner, \Brickoo\Event\Interfaces\ListenerAggregate {

        /**
         * Aggreagtes the events listening to.
         * The listeners have a priority of 100 (0-100 = first) to ensure the application functionality.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager the EventManager to attach the events to
         * @return void
         */
        public function aggregateListeners(\Brickoo\Event\Interfaces\Manager $EventManager)
        {
            $condition = function($Event) {return ($Event->Sender() instanceof \Brickoo\Core\Application);};
            $EventManager->attachListener(Events::EVENT_BOOT, array($this, 'boot'), 100, null, $condition);
            $EventManager->attachListener(Events::EVENT_SHUTDOWN, array($this, 'shutdown'), 100, null, $condition);
            $EventManager->attachListener(Events::EVENT_ROUTE_GET, array($this, 'getRequestRoute'), 100, null, $condition);
            $EventManager->attachListener(Events::EVENT_RESPONSE_GET, array($this, 'getResponse'), 100, null, $condition);
            $EventManager->attachListener(Events::EVENT_RESPONSE_SAVE, array($this, 'saveResponse'), 100, null, $condition);
        }

        /**
         * Listener called by the boot event.
         * Boots the Router by setting the available modules if they are not set.
         * @param \Brickoo\Event\Interfaces\Event $Event the event triggered
         * @return void
         */
        public function boot(\Brickoo\Event\Interfaces\Event $Event) {
            $Application = $Event->Sender();
            $Router = $Application->Router();

            if (! $Router->hasModules()) {
                $Router->setModules($Application->getModules());
            }
        }

        /**
         * Listener called if the application is going t shutdown.
         * @param \Brickoo\Event\Interfaces\Event $Event the event triggered
         * @return void
         */
        public function shutdown(\Brickoo\Event\Interfaces\Event $Event) {
            $this->stopSession($Event->Sender());
        }

        /**
         * Listener called by the request route retrieve event.
         * Retrives the RequestROute from the Router.
         * @param \Brickoo\Event\Interfaces\Event $Event the event triggered
         * @return \Brickoo\Routing\Interfaces\RequestRoute the matching request route
         */
        public function getRequestRoute(\Brickoo\Event\Interfaces\Event $Event) {
            return $Event->Sender()->Router()->getRequestRoute();
        }

        /**
         * Start the session if the route did required a session.
         * Notifies that the session can be configured.
         * @return \Brickoo\Core\Runner
         */
        public function startSession(\Brickoo\Core\Application $Application) {
            if ($Application->Route()->getModuleRoute()->isSessionRequired() &&
                (! $Application->SessionManager()->hasSessionStarted())
            ){
                $Application->EventManager()->notify(new Event(
                    Events::EVENT_SESSION_CONFIGURE, $Application, array('SessionManager' => $Application->SessionManager())
                ));
                $Application->SessionManager()->start();
            }

            return $this;
        }

        /**
         * Stops the session if the route did required a session and the session has been started.
         * @return \Brickoo\Core\Runner
         */
        public function stopSession($Application) {
            if ($Application->SessionManager()->hasSessionStarted()) {
                $Application->SessionManager()->stop();
            }

            return $this;
        }

        /**
         * Listener called if the request by the response get event.
         * Asks the EventManager for a request response to load.
         * @param \Brickoo\Event\Interfaces\Event $Event the event triggered
         * @return \Brickoo\Core\Interfaces\Response or null if no response has been returned
         */
        public function getResponse(\Brickoo\Event\Interfaces\Event $Event) {
            $Response        = null;
            $RequestRoute    = $Event->Sender()->Route();

            $this->startSession($Event->Sender());

            if ($RequestRoute->getModuleRoute()->isCacheable()) {
                $Response = $Event->EventManager()->ask(new Event(
                    Events::EVENT_RESPONSE_LOAD, $Event->Sender(), array('Route' => $RequestRoute)
                ));
            }

            return $Response;
        }

        /**
         * Listener called of the request could be cached.
         * Stops the event to be further processed if the route configuration does not support caching.
         * @param \Brickoo\Event\Interfaces\Event $Event the event triggered
         * @return void
         */
        public function saveResponse(\Brickoo\Event\Interfaces\Event $Event) {
            if (! $Event->Sender()->Route()->getModuleRoute()->isCacheable()) {
                $Event->stop();
            }
        }

    }