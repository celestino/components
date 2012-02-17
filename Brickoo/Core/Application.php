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

    use Brickoo\Validator\TypeValidator;

    /**
     * Application framework main class.
     * Used to have an global Registry which is part managed by the framework.
     * Holds an object of the \Brickoo\Memory\Registry.
     * Defines the Registry identifers reserved.
     * Contains methods to make registration or checks easier.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Application
    {

        /**
         * Defines the BrickOO Version.
         * @var string
         */
        const VERSION = 'DEV{3.0}';

        /**
         * Defines the Registry reserved indetifiers.
         * @var array
         */
        protected $reservedIdentifiers = array(
            'application'            => 'application',
            'logdirectory'           => 'application.log.directory',
            'cachedirectory'         => 'application.cache.directory',
            'environment'            => 'application.environment',
            'modules'                => 'application.modules',
            'router'                 => 'application.router',
            'requestroute'           => 'application.request.route',
            'logger'                 => 'application.logger',
            'sessionmanager'         => 'application.session.manager',
            'databaseconfig'         => 'application.database.config',
            'errorhandler'           => 'application.error.handler',
            'exceptionhandler'       => 'application.exception.handler',
            'request'                => 'application.request',
            'response'               => 'application.response',
            'autoloader'             => 'application.autoloader',
            'responsecachemanager'   => 'application.response.cache.manager',
            'cachemanager'           => 'application.cache.manager'
        );

        /**
         * Holds an instance of the Registry class.
         * @var \Brickoo\Core\Interfaces\RegistryInterface
         */
        protected $_Registry;

        /**
         * Returns the Registry dependency.
         * @return \Brickoo\Core\Interfaces\RegistryInterface
         */
        public function Registry()
        {
            return $this->_Registry;
        }

        /**
         * Holds an instance of the Request class.
         * @var \Brickoo\Core\Interfaces\RequestInterface
         */
        protected $_Request;

        /**
         * Return the Request dependency.
         * @return \Brickoo\Core\Interfaces\RequestInterface
         */
        public function Request()
        {
            return $this->_Request;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * Registers the Application and Request to the Registry.
         * @param \Brickoo\Core\Interfaces\RegistryInterface $Registry the Registry dependency to inject
         * @param \Brickoo\Core\Interfaces\RequestInterface $Request the Request dependency to inject
         * @return void
         */
        public function __construct(
            \Brickoo\Core\Interfaces\RegistryInterface $Registry,
            \Brickoo\Core\Interfaces\RequestInterface $Request
        )
        {
            $this->_Registry   = $Registry;
            $this->_Request    = $Request;
            $this->Registry()->register($this->reservedIdentifiers['request'], $Request);
            $this->Registry()->register($this->reservedIdentifiers['application'], $this);
        }

        /**
         * Returns the full BrickOO version with prefix and suffix.
         * @return string the full BrickOO version
         */
        public function getVersion()
        {
            return self::VERSION;
        }

        /**
         * Returns the BrickOO version number without prefix or suffix.
         * @return string the BrickOO version number
         */
        public function getVersionNumber()
        {
            preg_match('~\{(?<versionNumber>[0-9\.]+)\}~', self::VERSION, $matches);
            return $matches['versionNumber'];
        }

        /**
         * Registers the available modules to the Registry.
         * @param array $modules the available modules to register
         * @return \Brickoo\Core\Application
         */
        public function registerModules(array $modules)
        {
            foreach($modules as $index => $moduleDirectory) {
                $modules[$index] = rtrim($moduleDirectory, '/\\') . DIRECTORY_SEPARATOR;
            }

            $this->Registry()->register($this->reservedIdentifiers['modules'], $modules);

            return $this;
        }

        /**
         * Checks if a module has been registered and should be available.
         * @param string $moduleName the module name to check
         * @return boolean check result
         */
        public function isModuleAvailable($moduleName)
        {
            if (! $this->Registry()->isRegistered($this->reservedIdentifiers['modules'])) {
                return false;
            }

            TypeValidator::IsString($moduleName);

            return array_key_exists($moduleName, $this->Registry()->get($this->reservedIdentifiers['modules']));
        }

        /**
         * Returns the module absolute path to the root directory.
         * @param string $moduleName the module to return the path from
         * @throws Exceptions\ModuleNotAvailableException if the module is not available
         * @return string the module absolute path to the root directory
         */
        public function getModulePath($moduleName)
        {
            TypeValidator::IsString($moduleName);

            if (! $this->isModuleAvailable($moduleName)) {
                throw new Exceptions\ModuleNotAvailableException($moduleName);
            }

            $modules = $this->Registry()->get($this->reservedIdentifiers['modules']);

            return $modules[$moduleName];
        }

        /**
         * Registers the cache directory to the Registry.
         * @param string $cacheDirectory the cache directory to register
         * @return \Brickoo\Core\Application
         */
        public function registerCacheDirectory($cacheDirectory)
        {
            TypeValidator::IsString($cacheDirectory);

            $this->Registry()->register(
                $this->reservedIdentifiers['cachedirectory'],
                rtrim($cacheDirectory, '/\\') . DIRECTORY_SEPARATOR
            );

            return $this;
        }

        /**
         * Registers the log directory to the Registry.
         * @param string $logDirectory the log directory to register
         * @return \Brickoo\Core\Application
         */
        public function registerLogDirectory($logDirectory)
        {
            TypeValidator::IsString($logDirectory);

            $this->Registry()->register(
                $this->reservedIdentifiers['logdirectory'],
                rtrim($logDirectory, '/\\') . DIRECTORY_SEPARATOR
            );

            return $this;
        }

        /**
         * Returns the value of the application registered identifier.
         * @param string $identifier the registered identifier
         * @return mixed the value of the registered identifier or null if it is not registered
         */
        public function __get($identifier)
        {
            TypeValidator::IsString($identifier);

            $reservedIdentifier = strtolower($identifier);
            if (array_key_exists($reservedIdentifier, $this->reservedIdentifiers) &&
                ($identifier = $this->reservedIdentifiers[$reservedIdentifier]) &&
                $this->Registry()->isRegistered($identifier)
            ) {
                return $this->Registry()->get($identifier);
            }
        }

        /**
         * Registers an application identifier with its value.
         * @param string $method the method to call
         * @param array $arguments the value to register
         * @throws \BadMethodCallException if the method could not be converted
         * @return \Brickoo\Core\Application
         */
        public function __call($method, $arguments)
        {
            if (! empty($arguments) &&
                preg_match('~^register(?<identifier>[a-z]+)$~i', $method, $matches) &&
                array_key_exists(($identifier = strtolower($matches['identifier'])), $this->reservedIdentifiers)
            ) {
                $this->Registry()->register($this->reservedIdentifiers[$identifier], array_shift($arguments));
                return $this;
            }

            throw new \BadMethodCallException(sprintf('The method `%s` is not available.', $method));
        }

        /**
         * Layt initiliaziation of the Router dependency.
         * @return \Brickoo\Routing\Interfaces\RouterInterface
         */
        public function Router()
        {
            if (($Router = $this->Router) === null) {
                $Router = new \Brickoo\Routing\Router($this->Request);

                if (($directory = $this->cacheDirectory) !== null) {
                    $Router->setCacheDirectory($directory);
                }

                $Router->setModules(($modules = $this->modules) ?: array());

                $this->registerRouter($Router);
            }

            return $Router;
        }

        /**
         * Configures the Controller by adding the dependencies.
         * @param \Brickoo\Core\Interfaces\ControllerInterface $Controller the Controller to configure
         * @return \Brickoo\Core\Application
         */
        public function configureController(\Brickoo\Core\Interfaces\ControllerInterface $Controller)
        {
            $Controller->Registry($this->Registry())
                       ->Request($this->Request())
                       ->Application($this);

            if ($RequestRoute = $this->RequestRoute) {
                $Controller->Route($RequestRoute);
            }

            return $this;
        }

    }