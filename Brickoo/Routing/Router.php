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

    namespace Brickoo\Routing;

    use Brickoo\Core,
        Brickoo\Event,
        Brickoo\Routing\Events as RouterEvents,
        Brickoo\Validator\TypeValidator;

    /**
     * Router
     *
     * Router class which handles the routes collected.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Router implements Interfaces\RouterInterface {

        /**
         * Holds an instance of the Request class.
         * @var \Brickoo\Core\Interfaces\RequestInterface
         */
        protected $Request;

        /**
         * Returns the Request instance implementing the RequestInterface.
         * @return \Brickoo\Core\Interfaces\RequestInterface
         */
        public function getRequest() {
            return $this->Request;
        }

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
         * @return object Router if overwritten otherwise the dependency
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
         * Lazy initialization of the RouteCollection dependecy.
         * @param \Brickoo\Routing\Interfaces\RouteCollectionInterface $RouteCollection the collection of routes
         * @return \Brickoo\Routing\Interfaces\RouterCollectionInterface
         */
        public function RouteCollection(\Brickoo\Routing\Interfaces\RouteCollectionInterface $RouteCollection = null) {
            return $this->getDependency(
                'RouteCollection',
                '\Brickoo\Routing\Interfaces\RouteCollectionInterface',
                function(){return new RouteCollection();},
                $RouteCollection
            );
        }

        /**
         * Lazy initialization of the RouteFinder dependecy.
         * @param \Brickoo\Routing\Interfaces\RouteFinderInterface $RouteFinder the RouteFinder dependency
         * @return \Brickoo\Routing\Interfaces\RouteFinderInterface
         */
        public function RouteFinder(\Brickoo\Routing\Interfaces\RouteFinderInterface $RouteFinder = null) {
            return $this->getDependency(
                'RouteFinder',
                '\Brickoo\Routing\Interfaces\RouteFinderInterface',
                function($Router){
                    return new RouteFinder(
                        $Router->RouteCollection(), $Router->getRequest(), $Router->Aliases()
                    );
                },
                $RouteFinder
            );
        }

        /**
         * Lazy initialization of the Aliases dependecy.
         * @param \Brickoo\Memory\Interfaces\ContainerInterface $Aliases the Container dependency
         * @return \Brickoo\Memory\Interfaces\ContainerInterface
         */
        public function Aliases(\Brickoo\Memory\Interfaces\ContainerInterface $Aliases = null) {
            return $this->getDependency(
                'Aliases',
                '\Brickoo\Memory\Interfaces\ContainerInterface',
                function(){return new \Brickoo\Memory\Container();},
                $Aliases
            );
        }

        /**
         * Lazy initialization of the EventManager dependecy.
         * @param \Brickoo\Event\Interfaces\ManagerInterface $EventManager the EventManager dependency
         * @return \Brickoo\Event\Interfaces\ManagerInterface
         */
        public function EventManager(\Brickoo\Event\Interfaces\ManagerInterface $EventManager = null) {
            return $this->getDependency(
                'EventManager',
                '\Brickoo\Event\Interfaces\ManagerInterface',
                function(){return new \Brickoo\Event\Manager();},
                $EventManager
            );
        }

        /**
         * Holds the routes file name search at the modules.
         * @var string
         */
        protected $routesFilename;

        /**
         * Returns the routes file name.
         * @return string the routes file name
         */
        public function getRoutesFilename() {
            return $this->routesFilename;
        }

        /**
         * Sets the  routes file name searched in the modules directory.
         * @param string $routesFilename the routes file name
         * @return \Brickoo\Routing\Router
         */
        public function setRoutesFilename($routesFilename) {
            TypeValidator::IsString($routesFilename);

            $this->routesFilename = $routesFilename;

            return $this;
        }

        /**
         * Holds the modules to analyze for application routes.
         * @var array
         */
        protected $modules;

        /**
         * Returns the modules available.
         * @return array the modules available
         */
        public function getModules() {
            return $this->modules;
        }

        /**
         * Sets the modules to load the routes from if available.
         * If the modules a set directly, the modules will not be available through the Brickoo Registry.
         * @param array $modules the modules to load the routes from
         * @throws Core\Exceptions\ValueOverwriteException if trying to overwrite the available modules
         * @return \Brickoo\Routing\Router
         */
        public function setModules(array $modules) {
            if (! empty($this->modules)) {
                throw new Core\Exceptions\ValueOverwriteException('Router::modules');
            }

            $this->modules = $modules;

            return $this;
        }

        /**
         * Checks if any modules are available.
         * @return boolean check result
         */
        public function hasModules() {
            return (! empty($this->modules));
        }

        /**
         * Holds the requeste Route instance.
         * @var Brickoo\Routing\Interfaces\RequestRouteInterface
         */
        protected $RequestRoute;

        /**
         * Sets the requested Route for further routing.
         * @param \Brickoo\Routing\Interfaces\RequestRouteInterface $RequestRoute the route matched the request
         * @throws \Brickoo\Core\Exceptions\ValueOverwriteException if trying to overwrite the request route
         * @return \Brickoo\Routing\Router
         */
        public function setRequestRoute(\Brickoo\Routing\Interfaces\RequestRouteInterface $RequestRoute) {
            if ($this->RequestRoute !== null) {
                throw new Core\Exceptions\ValueOverwriteException('Router::RequestRoute');
            }

            $this->RequestRoute = $RequestRoute;

            return $this;
        }

        /**
         * Checks if the requested Route has been found and is set.
         * @return boolean check result
         */
        public function hasRequestRoute() {
            return ($this->RequestRoute instanceof Interfaces\RequestRouteInterface);
        }

        /**
         * Returns the request matching route.
         * If the Manager is available the proceded routes will be cached.
         * @throws Routing\Exceptions\RequestedHasNoRouteException if the request has not a matching Route
         * @return \Brickoo\Routing\Route
         */
        public function getRequestRoute() {
            if ($this->hasRequestRoute()) {
                return $this->RequestRoute;
            }

            if (! $this->RouteCollection()->hasRoutes()) {
                $this->loadModulesRoutes();
            }

            try {
                $this->setRequestRoute($this->RouteFinder()->find());
            }
            catch (\Brickoo\Routing\Exceptions\RequestHasNoRouteException $Exception) {
                $this->EventManager()->notify(new Event\Event(
                    RouterEvents::EVENT_ERROR, $this, array('Exception' => $Exception)
                ));
                throw $Exception;
            }

            $this->saveModulesRoutes();

            return $this->RequestRoute;
        }

        /**
        * Class constructor.
        * Injects a Request dependency implementing the Core}Interfaces\RequestInterface
        * Initializes the class properties.
        * @return void
        */
        public function __construct(\Brickoo\Core\Interfaces\RequestInterface $Request) {
            $this->Request            = $Request;
            $this->RequestRoute       = null;
            $this->dependencies       = array();
            $this->modules            = array();
            $this->aliases            = array();
            $this->routesFilename     = 'routes.php';
        }

        /**
         * Loads the Modules routes by asking over an event or collecting from filesystem.
         * @return \Brickoo\Routing\Router
         */
        public function loadModulesRoutes() {
            if (($RouteCollection = $this->EventManager()->ask(new Event\Event(RouterEvents::EVENT_LOAD, $this))) &&
                ($RouteCollection instanceof Interfaces\RouteCollectionInterface)
            ){
                $this->RouteCollection($RouteCollection);
            }
            else {
                $this->collectModulesRoutes();
            }

            return $this;
        }

        /**
         * Saves the collected routes over an event notification.
         * @return \Brickoo\Routing\Router
         */
        public function saveModulesRoutes() {
            $this->EventManager()->notify(new Event\Event(
                RouterEvents::EVENT_SAVE, $this, array('RouteCollection' => $this->RouteCollection())
            ));

            return $this;
        }

        /**
         * Collectes the routes available to add to the RouteCollection.
         * Searches through all available modules available to require the route collections.
         * This requires the registered modules, which is normaly done by the FrontController.
         * @return void
         */
        public function collectModulesRoutes() {
            if ($modules = $this->getModules()) {
                foreach($modules as $modulePath) {
                    if (file_exists(($routingFilename = $modulePath . $this->getRoutesFilename()))&&
                        ($ModuleRouteCollection = (require ($routingFilename))) &&
                        ($ModuleRouteCollection instanceof Interfaces\RouteCollectionInterface) &&
                        $ModuleRouteCollection->hasRoutes()
                    ){
                        $this->RouteCollection()->addRoutes($ModuleRouteCollection->getRoutes());
                    }
                }
            }
        }

    }