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

    namespace Brickoo\Http;

    use Brickoo\Core,
        Brickoo\Event,
        Brickoo\Validator\TypeValidator;

    /**
     * Implements methods to handle HTTP requests.
     * This class is listening to the Brickoo\Core\Application events.
     * The cache events are NOT implemented within this class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Application implements Event\Interfaces\ListenerAggregateInterface
    {

        /**
         * Holds an flag for preventing duplicate listener aggregation.
         * @var boolean
         */
        protected $listenerAggregated;

        /**
         * Registers the listeners to the EventManager.
         * This method is automaticly called by Brickoo\Core\Application::run if injected
         * since this application implements the ListenerAggreagteInterface.
         * @param \Brickoo\Event\Interfaces\EventManagerInterface $EventManager
         * @return void
         */
        public function aggregateListeners(\Brickoo\Event\Interfaces\EventManagerInterface $EventManager)
        {
            if ($this->listenerAggregated !== true) {
                $EventManager->attachListener(Core\Application::EVENT_ROUTER_BOOT, array($this, 'routerBoot'));
                $EventManager->attachListener(Core\Application::EVENT_ROUTER_ERROR, array($this, 'displayError'), 0, array('Exception'));
                $EventManager->attachListener(Core\Application::EVENT_SESSION_START, array($this, 'startSession'), 0, array('SessionManager'));
                $EventManager->attachListener(Core\Application::EVENT_SESSION_STOP, array($this, 'stopSession'), 0, array('SessionManager'));
                $EventManager->attachListener(Core\Application::EVENT_RESPONSE_GET, array($this, 'getResponse'));
                $EventManager->attachListener(Core\Application::EVENT_RESPONSE_SEND, array($this, 'sendResponse'), 0, array('Response'));
                $EventManager->attachListener(Core\Application::EVENT_APPLICATION_ERROR, array($this, 'displayError'), 0, array('Exception'));

                $this->eventsRegistered = true;
            }
        }

        /**
         * Boot route of the Router.
         * Sets the available modules if they are not set.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the application event
         * @return \Brickoo\Http\Application
         */
        public function routerBoot(\Brickoo\Event\Interfaces\EventInterface $Event)
        {
            if (($Application = $Event->Sender()) instanceof \Brickoo\Core\Application) {
                if (! $Application->Router()->hasModules()) {
                    $Application->Router()->setModules($Application->getModules());
                }
            }

            return $this;
        }

        /**
         * Sends a simple http response if an exception is throwed by the router
         * or within the Brickoo\Core\Application::run method.
         * This is just a dummy to display SOMETHING on errors.
         * @param \Exception $Exception the Exception throwed
         * @return void
         */
        public function displayError(\Exception $Exception)
        {
            $Response = new \Brickoo\Http\Response();
            $Response->setContent("<html><head><title></title></head><body>\r\n".
                "<h1>This is not the response you are looking for...</h1>\r\n".
                "<div>(<b>Exception</b>: ". $Exception->getMessage() .")\r\n".
                "</body></html>"
            );
            $Response->send();
        }

        /**
         * Starts the session.
         * This method is called on the event Brickoo\Core\Application::EVENT_SESSION_START
         * @param \Brickoo\Http\Session\Interfaces\SessionManagerInterface $SessionManager
         * @return \Brickoo\Http\Application
         */
        public function startSession(\Brickoo\Http\Session\Interfaces\SessionManagerInterface $SessionManager)
        {
            $SessionManager->start();
            return $this;
        }

        /**
         * Stops the session.
         * This method is called on the event Brickoo\Core\Application::EVENT_SESSION_STOP.
         * @param \Brickoo\Http\Session\Interfaces\SessionManagerInterface $SessionManager
         * @return \Brickoo\Http\Application
         */
        public function stopSession(\Brickoo\Http\Session\Interfaces\SessionManagerInterface $SessionManager)
        {
            $SessionManager->stop();
            return $this;
        }

        /**
         * Returns always a fresh response.
         * Notifies the module boot event listeners.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the application event asking
         * @return \Brickoo\Core\Interfaces\ResponseInterface
         */
        public function getResponse(\Brickoo\Event\Interfaces\EventInterface $Event)
        {
            if (($RequestRoute = $Event->getParam('Route')) instanceof \Brickoo\Routing\Interfaces\RequestRouteInterface) {

                $RouteController = $RequestRoute->getModuleRoute()->getController();
                if (! $RouteController['static']) {
                    $RouteController['controller'] = new $RouteController['controller'];
                }

                $Event->EventManager()->notify(new Event\Event(
                    Core\Application::EVENT_MODULE_BOOT, $Event->Sender(), array('controller' => $RouteController)
                ));

                return $RouteController['controller']->$RouteController['method']($Event->Sender());
            }
        }

        /**
         * Sends the Response headers and content.
         * @param \Brickoo\Core\Interfaces\ResponseInterface $Response the request response
         * @return \Brickoo\Core\Application
         */
        public function sendResponse(\Brickoo\Core\Interfaces\ResponseInterface $Response)
        {
            $Response->send();

            return $this;
        }

    }