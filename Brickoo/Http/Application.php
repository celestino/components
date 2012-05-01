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
        Brickoo\Module,
        Brickoo\Validator\TypeValidator;

    /**
     * This class is listening to the Brickoo\Core\Application events.
     * The cache, routing, session events are NOT implemented within this class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Application implements Event\Interfaces\ListenerAggregate {

        /**
        * Holds the class dependencies.
        * @var array
        */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependency the dependecy to inject
         * @return object Application if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependency = null) {
            if ($Dependency instanceof $interface) {
                $this->dependencies[$name] = $Dependency;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the Response dependency.
         * @param \Brickoo\Core\Interfaces\Response $Response the Response dependency to inject
         * @return \Brickoo\Core\Interfaces\Response
         */
        public function Response(\Brickoo\Core\Interfaces\Response $Response = null) {
            return $this->getDependency(
                'Response',
                '\Brickoo\Core\Interfaces\Response',
                function() {return new Response();},
                $Response
            );
        }

        /**
         * Holds an flag for preventing duplicate listener aggregation.
         * @var boolean
         */
        protected $listenerAggregated;

        /**
         * Registers the listeners to the EventManager.
         * This method is automaticly called by Brickoo\Core\Application::run if injected
         * since this application implements the ListenerAggreagte.
         * The listeners have all the lowest priority (0-100 = low) to be overriden.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return void
         */
        public function aggregateListeners(\Brickoo\Event\Interfaces\Manager $EventManager) {
            if ($this->listenerAggregated !== true) {
                $EventManager->attachListener(
                    Core\Events::RESPONSE_GET, array($this, 'run'), 0, null,
                        function($Event){return ($Event->getParam('Route') instanceof \Brickoo\Routing\Interfaces\RequestRoute);}
                );
                $EventManager->attachListener(
                    Core\Events::RESPONSE_SEND, array($this, 'sendResponse'), 0, array('Response'),
                    function($Event){return ($Event->getParam('Response') instanceof \Brickoo\Core\Interfaces\Response);}

                );
                $EventManager->attachListener(
                    Module\Events::MODULE_ERROR, array($this, 'displayModuleError'), 0, array('Exception')
                );
                $EventManager->attachListener(
                    Core\Events::ERROR, array($this, 'displayError'), 0, array('Exception')
                );
                $EventManager->attachListener(
                    Core\Events::RESPONSE_MISSING, array($this, 'displayResponseError')
                );

                $this->listenerAggregated = true;
            }
        }

        /**
         * Sends a simple http response if an exception is throwed by the router.
         * or within the Brickoo\Core\Application::run method.
         * This is just a dummy to display SOMETHING on errors.
         * @param \Exception $Exception the Exception throwed
         * @return void
         */
        public function displayError(\Exception $Exception) {
            $this->Response()->setContent("<html><head><title></title></head><body>\r\n".
                "<h1>This is not the response you are looking for...</h1>\r\n".
                "<div>(<b>Exception</b>: ". $Exception->getMessage() .")\r\n".
                "</body></html>"
            );
            $this->Response()->send();
        }

        /**
         * Sends a simple http response if an exception is throwed by the module.
         * or within the Brickoo\Core\Application::run method.
         * This is just a dummy to display SOMETHING on module errors.
         * @param \Exception $Exception the Exception throwed
         * @return void
         */
        public function displayModuleError(\Exception $Exception) {
            $this->Response()->setContent("<html><head><title></title></head><body>\r\n".
                "<h1>Something did go wrong within the module...</h1>\r\n".
                "<div>(<b>Exception</b>: ". $Exception->getMessage() .")\r\n".
                "</body></html>"
            );
            return $this->Response();
        }

        /**
         * Sends a simple http response if the response is missed.
         * It stops the event to be processed by an other listener.
         * This is just a dummy to display SOMETHING on errors.
         * @param \Exception $Exception the Exception throwed
         * @return void
         */
        public function displayResponseError(\Brickoo\Event\Event $Event) {
            $this->Response()->setContent("<html><head><title></title></head><body>\r\n".
                "<h1>Who likes to get a beer?</h1>\r\n".
                "<div>(<b>Exception</b>: Controller did not return a response.)\r\n".
                "</body></html>"
            );
            $this->Response()->send();
            $Event->stop();
        }

        /**
         * Returns always a fresh response.
         * Notifies the module boot event listeners.
         * @param \Brickoo\Event\Interfaces\Event $Event the application event asking
         * @return \Brickoo\Core\Interfaces\Response
         */
        public function run(\Brickoo\Event\Interfaces\Event $Event) {
            $RequestRoute = $Event->getParam('Route');
            $Response = null;

            try {
                $RouteController = $RequestRoute->getModuleRoute()->getController();

                $Event->EventManager()->notify(new Event\Event(
                    Module\Events::MODULE_BOOT, $Event->Sender(), array(
                        'controller' => $RouteController['controller'],
                        'method'     => $RouteController['method']
                    )
                ));

                if (! $RouteController['static']) {
                    $RouteController['controller'] = new $RouteController['controller']($Event->Sender());
                    $Response = $RouteController['controller']->$RouteController['method']();
                }
                else {
                    $Response = call_user_func(
                        array($RouteController['controller'], $RouteController['method']),
                        $Event->Sender()
                    );
                }

                $Event->EventManager()->notify(new Event\Event(Module\Events::MODULE_SHUTDOWN, $Event->Sender()));
            }
            catch (\Exception $Exception) {
                $Response = $Event->EventManager()->ask(new Event\Event(
                    Module\Events::MODULE_ERROR, $Event->Sender(), array('Exception' => $Exception)
                ));
            }

            return $Response;
        }

        /**
         * Sends the Response headers and content.
         * @param \Brickoo\Core\Interfaces\Response $Response the request response
         * @return \Brickoo\Core\Application
         */
        public function sendResponse(\Brickoo\Core\Interfaces\Response $Response) {
            $Response->send();

            return $this;
        }

    }