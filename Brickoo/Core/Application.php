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

    use Brickoo\Event\Event,
        Brickoo\Validator\TypeValidator;

    /**
     * This class implements methods to handle the application configuration.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Application {

        /**
         * Defines the BrickOO Version.
         * @var string
         */
        const VERSION = 'DEV-3.0';

        /**
         * Defines the Registry reserved indetifiers.
         * @var array
         */
        protected $reservedIdentifiers = array(
            'autoloader'             => 'brickoo.autoloader',
            'publicdirectory'        => 'brickoo.public.directory',
            'modules'                => 'brickoo.modules',
            'application'            => 'brickoo.application'
        );

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
         * @return object the Application if overwritten otherwise the dependency
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
         * Lazy intialization of the Registry dependency.
         * @param \Brickoo\Memory\Interfaces\Registry $Registry the Registy dependency to inject
         * @return \Brickoo\Memory\Interfaces\Registry
         */
        public function Registry(\Brickoo\Memory\Interfaces\Registry $Registry = null) {
            return $this->getDependency(
                'Registry',
                '\Brickoo\Memory\Interfaces\Registry',
                function (){return new \Brickoo\Memory\Registry();},
                $Registry
            );
        }

        /**
        * Lazy initialization of the Request dependency.
        * @param \Brickoo\Core\Interfaces\Request $Request the Request dependency to inject
        * @return \Brickoo\Core\Interfaces\Request
        */
        public function Request(\Brickoo\Core\Interfaces\Request $Request = null) {
            return $this->getDependency(
                'Request',
                '\Brickoo\Core\Interfaces\Request',
                function (){
                    $Request = new \Brickoo\Http\Request();
                    $Request->importFromGlobals();
                    return $Request;
                },
                $Request
            );
        }

        /**
         * Lazy initializiation of the Router dependency.
         * @param \Brickoo\Routing\Interfaces\Router $Router
         * @return \Brickoo\Routing\Interfaces\Router
         */
        public function Router(\Brickoo\Routing\Interfaces\Router $Router = null) {
            return $this->getDependency(
                'Router',
                '\Brickoo\Routing\Interfaces\Router',
                function ($Application){
                    $Router = new \Brickoo\Routing\Router($Application->Request());
                    $Router->EventManager($Application->EventManager());
                    return $Router;
                },
                $Router
            );
        }

        /**
         * Holds an instance of the request Route.
         * @param \Brickoo\Routing\Interfaces\RequestRoute $Route
         * @return \Brickoo\Routing\Interfaces\RequestRoute
         */
        public function Route(\Brickoo\Routing\Interfaces\RequestRoute $Route = null) {
            return $this->getDependency(
                'Route',
                '\Brickoo\Routing\Interfaces\RequestRoute',
                function (){throw new \Brickoo\Core\Exceptions\DependencyNotAvailableException('RequestRoute');},
                $Route
            );
        }

        /**
         * Lazy intialization of the static EventManager dependency.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return \Brickoo\Event\Interfaces\Manager
         */
        public function EventManager(\Brickoo\Event\Interfaces\Manager $EventManager = null) {
            return $this->getDependency(
                'EventManager',
                '\Brickoo\Event\Interfaces\Manager',
                function() {return \Brickoo\Event\Manager::Instance();},
                $EventManager
            );
        }

        /**
         * Lazy intialization of the SessionManager dependency.
         * @param \Brickoo\Http\Session\Interfaces\Manager $Manager
         * @return \Brickoo\Http\Session\Interfaces\Manager
         */
        public function SessionManager(\Brickoo\Http\Session\Interfaces\Manager $SessionManager = null) {
            return $this->getDependency(
                'SessionManager',
                '\Brickoo\Http\Session\Interfaces\Manager',
                function() {
                    return new \Brickoo\Http\Session\Manager(
                        new \Brickoo\Http\Session\Handler\CacheHandler()
                    );
                },
                $SessionManager
            );
        }

        /**
         * Lazy initialization of the Runner dependencyy.
         * @param \Brickoo\Core\Interfaces\Runner $Runner
         * @return \Brickoo\Core\Interfaces\Runner
         */
        public function Runner(\Brickoo\Core\Interfaces\Runner $Runner = null) {
            return $this->getDependency(
                'Runner',
                '\Brickoo\Core\Interfaces\Runner',
                function($Application) {return new \Brickoo\Core\Runner($Application);},
                $Runner
            );
        }

        /**
         * Returns the full BrickOO version with prefix and suffix.
         * @return string the full BrickOO version
         */
        public function getVersion() {
            return self::VERSION;
        }

        /**
         * Returns the BrickOO version number without prefix or suffix.
         * @return string the BrickOO version number
         */
        public function getVersionNumber() {
            preg_match('~(?<versionNumber>[0-9\.]+)~', self::VERSION, $matches);
            return $matches['versionNumber'];
        }

        /**
         * Registers the autoloader instance to the Registry.
         * @param \Brickoo\Core\Interfaces\Autoloader $Autoloader
         * @return \Brickoo\Core\Application
         */
        public function registerAutoloader(\Brickoo\Core\Interfaces\Autoloader $Autoloader) {
            $this->set($this->reservedIdentifiers['autoloader'], $Autoloader);

            return $this;
        }

        /**
         * Registers the available modules to the Registry.
         * @param array $modules the available modules to register
         * @return \Brickoo\Core\Application
         */
        public function registerModules(array $modules) {
            foreach($modules as $index => $moduleDirectory) {
                $modules[$index] = rtrim($moduleDirectory, '/\\') . DIRECTORY_SEPARATOR;
            }

            $this->set($this->reservedIdentifiers['modules'], $modules);

            return $this;
        }

        /**
         * Returns the available modules.
         * @return array the available modules.
         */
        public function getModules() {
            return (is_array(($modules = $this->get('modules'))) ? $modules : array());
        }

        /**
         * Checks if a module has been registered and should be available.
         * @param string $moduleName the module name to check
         * @return boolean check result
         */
        public function isModuleAvailable($moduleName) {
            TypeValidator::IsString($moduleName);

            return array_key_exists($moduleName, $this->getModules());
        }

        /**
         * Returns the module absolute path to the root directory.
         * @param string $moduleName the module to return the path from
         * @throws Exceptions\ModuleNotAvailableException if the module is not available
         * @return string the module absolute path to the root directory
         */
        public function getModulePath($moduleName) {
            TypeValidator::IsString($moduleName);

            if (! $this->isModuleAvailable($moduleName)) {
                throw new Exceptions\ModuleNotAvailableException($moduleName);
            }

            $modules = $this->getModules();

            return $modules[$moduleName];
        }

        /**
         * Registers a directory and his identifier to the Registry.
         * @param string $identifier the identifier to register
         * @param string $directoryPath the directory to register with
         * @throws Exceptions\DirectoryDoesNotExistException if the directory does not exist
         * @return \Brickoo\Http\Application
         */
        public function registerDirectory($identifier, $directoryPath) {
            TypeValidator::IsString($identifier);
            TypeValidator::IsString($directoryPath);

            if (! is_dir($directoryPath)) {
                throw new Exceptions\DirectoryDoesNotExistException($directoryPath);
            }

            $this->set($identifier, rtrim($directoryPath, '/\\') . DIRECTORY_SEPARATOR);

            return $this;
        }

        /**
         * Registers the public accessible directory to the Registry.
         * @param string $publicDirectory the public directory to register
         * @return \Brickoo\Http\Application
         */
        public function registerPublicDirectory($publicDirectory) {
            TypeValidator::IsString($publicDirectory);

            $this->set($this->reservedIdentifiers['publicdirectory'], rtrim($publicDirectory, '/\\') . '/');

            return $this;
        }

        /**
         * Checks if the public directory has been set.
         * @return boolean check result
         */
        public function hasPublicDirectory() {
            return $this->has('publicDirectory');
        }

        /**
         * Checks if an identifier is registered with to the Registry.
         * @param string $identifier the identifier to check
         * @return boolean check result
         */
        public function has($identifier) {
            TypeValidator::IsString($identifier);

            $reservedIdentifier = strtolower($identifier);
            if (array_key_exists($reservedIdentifier, $this->reservedIdentifiers)) {
                ($identifier = $this->reservedIdentifiers[$reservedIdentifier]);
            }

            return $this->Registry()->isRegistered($identifier);
        }

        /**
        * Returns the value of the application registered identifier.
        * @param string $identifier the registered identifier
        * @return mixed the value of the registered identifier or null if it is not registered
        */
        public function get($identifier) {
            TypeValidator::IsString($identifier);

            $reservedIdentifier = strtolower($identifier);
            if (array_key_exists($reservedIdentifier, $this->reservedIdentifiers)) {
                ($identifier = $this->reservedIdentifiers[$reservedIdentifier]);
            }

            if (! $this->Registry()->isRegistered($identifier)) {
                return null;
            }

            return $this->Registry()->get($identifier);
        }

        /**
         * Registers AND LOCKS the identifier to the registry.
         * @param string $identifier the identifier to register
         * @param mixed $value the value to add
         * @return \Brickoo\Core\Application
         */
        public function set($identifier, $value) {
            TypeValidator::IsString($identifier);

            $this->Registry()->register($identifier, $value)
                             ->lock($identifier);

            return $this;
        }

        /**
         * Returns the value of the registered identifier.
         * @param string $identifier the registered identifier
         * @return mixed the value of the registered identifier or null if it is not registered
         */
        public function __get($identifier) {
            return $this->get($identifier);
        }

        /**
         * Registers AND LOCKS the identifier to the Registry.
         * @param string $identifier the identifier to register
         * @param mixed $value the value to add
         * @return void
         */
        public function __set($identifier, $value) {
            $this->set($identifier, $value);
        }

        /**
         * Checks if the identifier is registered in the Registry.
         * @param string $identifier the identifier to check
         * @return boolean check result
         */
        public function __isset($identifier) {
            return $this->has($identifier);
        }

        /**
         * Runs the application.
         * Retrives the route responsible for the request.
         * Executes the events in a proper order.
         * @param object $MainApplication
         * @return \Brickoo\Core\Application
         */
        public function run($MainApplication = null) {
            $EventManager = $this->EventManager();

            if ($MainApplication !== null) {
                $this->set('application', $MainApplication);
                if ($MainApplication instanceof \Brickoo\Event\Interfaces\ListenerAggregate) {
                    $MainApplication->aggregateListeners($EventManager);
                }
            }

            if (($Runner = $this->Runner()) instanceof \Brickoo\Event\Interfaces\ListenerAggregate) {
                $Runner->aggregateListeners($EventManager);
            }

            try {
                $EventManager->notify(new Event(Events::EVENT_BOOT, $this));

                $Response = $EventManager->notify(new Event(Events::EVENT_RESPONSE_GET, $this));

                if ($Response instanceof Interfaces\Response) {
                    $EventManager->notifyOnce(
                        new Event(Events::EVENT_RESPONSE_CACHE, $this, array('Response' => $Response))
                    );
                    $EventManager->notifyOnce(
                        new Event(Events::EVENT_RESPONSE_SEND, $this, array('Response' => $Response))
                    );
                }
                else {
                    $EventManager->notify(new Event(Events::EVENT_RESPONSE_MISSING, $this));
                }

                $EventManager->notify(new Event(Events::EVENT_SHUTDOWN, $this));
            }
            catch(\Exception $Exception) {
                $EventManager->notify(
                    new Event(Events::EVENT_ERROR, $this, array('Exception' => $Exception))
                );
            }

            return $this;
        }

    }