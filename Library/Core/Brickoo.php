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

    namespace Brickoo\Library\Core;

    use Brickoo\Library\Memory;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Brickoo framework main class.
     * Used to have an global Registry which is part managed by the framework.
     * Holds an object of the \Brickoo\Library\Memory\Registry.
     * Defines the Registry identifers reserved.
     * Contains methods to make registration or checks easier.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Brickoo implements Interfaces\BrickooInterface
    {

        /**
         * Defines the BrickOO Version.
         * @var string
         */
        const VERSION = 'DEV{3.0}';

        /**
         * Defines the available environments.
         * @var integer
         */
        const ENVIRONMENT_DEVELOPMENT    = 1;
        const ENVIRONMENT_PRODUCTION     = 2;

        /**
         * Defines the Registry reserved indetifiers.
         * @var string
         */
        const ENVIRONMENT           = 'configuration.environment';
        const DIRECTORY_LOG         = 'directory.logs';
        const DIRECTORY_CACHE       = 'directory.cache';
        const MODULES               = 'object.modules';
        const AUTOLOADER            = 'object.autoloader';
        const CACHE_MANAGER         = 'object.cache.manager';
        const FRONT_CONTROLLER      = 'object.front.controller';
        const DATABASES_CONFIG      = 'object.database.config';

        /**
         * Holds an instance of the Registry class.
         * @var \Brickoo\Library\Memory\Interfaces\RegistryInterface
         */
        protected static $Registry;

        /**
         * Lazy initialization of the Registry dependency.
         * Returns the holded Registry instance.
         * @return \Brickoo\Library\Memory\Interfaces\RegistryInterface
         */
        public function getRegistry()
        {
            if (static::$Registry === null) {
                $this->injectRegistry(new Memory\Registry());
            }

            return static::$Registry;
        }

        /**
         * Injects the Registry dependency to use.
         * @param Brickoo\Library\Memory\Interfaces\RegistryInterface $Registry the Registry dependency
         * @throws DependencyOverwriteException if trying to override dependency
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function injectRegistry(\Brickoo\Library\Memory\Interfaces\RegistryInterface $Registry)
        {
            if (static::$Registry !== null) {
                throw new Exceptions\DependencyOverwriteException('RegistryInterface');
            }

            static::$Registry = $Registry;

            return $this;
        }

        /**
         * Shortcut to retrieve a value from the Registry.
         * @param string|integer $identifier the identifier to retrieve the value from
         * @return mixed the value holded by the identifier
         */
        public function get($identifier)
        {
            return $this->getRegistry()->getRegistered($identifier);
        }

        /**
         * Shortcut to register a new identifier and add the value to it.
         * This method also locks(!) the identifier, since the registry should
         * not allow to overwrite an registered system wide identifier.
         * @param string|integer $identifier the identifier to register
         * @param string $value the valuue to be holded
         * @return \Brickoo\Library\Core\Brickoo
         */
        public  function register($identifier, $value)
        {
            $this->getRegistry()->register($identifier, $value)->lock($identifier);

            return $this;
        }

        /**
         * Shorcut to check if an identifier is registered.
         * @param string|integer $identifier the identifier to check its availability
         * @return boolean check result
         */
        public function isRegistered($identifier)
        {
            return isset($this->getRegistry()->$identifier);
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
         * Returns the registered Autoloader.
         * @return \Brickoo\Library\Core\Interfaces\AutoloaderInterface
         */
        public function getAutoloader()
        {
            return $this->get(self::AUTOLOADER);
        }

        /**
         * Registers the Autoloader to the Registry.
         * @param \Brickoo\Library\Core\Interfaces\AutoloaderInterface $Autoloader the Autoloader to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerAutoloader(\Brickoo\Library\Core\Interfaces\AutoloaderInterface $Autoloader)
        {
            $this->register(self::AUTOLOADER, $Autoloader);

            return $this;
        }

        /**
         * Registers the available modules to the Registry.
         * @param array $modules the available modules to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerModules(array $modules)
        {
            TypeValidator::IsArray($modules);

            $Autoloader = $this->get(self::AUTOLOADER);

            foreach($modules as $moduleName => $modulePath) {
                $Autoloader->registerNamespace($moduleName, $modulePath);
                $modules[$moduleName] = rtrim($modulePath, '/\\') . DIRECTORY_SEPARATOR;
            }

            $this->register(self::MODULES, $modules);

            return $this;
        }

        /**
         * Returns the available modules.
         * @return array the available modules
         */
        public function getModules()
        {
            return $this->get(self::MODULES);
        }

        /**
         * Checks if a module has been registered and should be available.
         * @param unknown_type $moduleName the module name to check
         * @return boolean check result
         */
        public function isModuleAvailable($moduleName)
        {
            if (! $this->getRegistry()->isRegistered(self::MODULES)) {
                return false;
            }

            $modules = $this->getRegistry()->getRegistered(self::MODULES);

            return array_key_exists($moduleName, $modules);
        }

        /**
         * Returns the module absolute path to the root directory.
         * @param unknown_type $moduleName the module to return the path from
         * @throws Exceptions\ModuleNotAvailableException if the module is not available
         * @return string the module absolute path to the root directory
         */
        public function getModulePath($moduleName)
        {
            if (! $this->isModuleAvailable($moduleName)) {
                throw new Exceptions\ModuleNotAvailableException($moduleName);
            }

            $modules = $this->getRegistry()->getRegistered(self::MODULES);

            return $modules[$moduleName];
        }

        /**
         * Registers the current environment to the Registry.
         * @param integer $environment the environment currently used
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerEnvironment($environment)
        {
            TypeValidator::IsInteger($environment);

            $this->register(self::ENVIRONMENT, $environment);

            return $this;
        }

        /**
         * Checks if the environment is currently used.
         * @param integer $environment the environment to check
         * @return boolean check result
         */
        public function isEnvironment($environment)
        {
            TypeValidator::IsInteger($environment);

            return (boolean)($environment & $this->get(self::ENVIRONMENT));
        }

        /**
         * Registers the cache directory to the Registry.
         * @param string $cacheDirectory the cache directory to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerCacheDirectory($cacheDirectory)
        {
            TypeValidator::IsString($cacheDirectory);

            $this->register(self::DIRECTORY_CACHE, rtrim($cacheDirectory, '/\\') . DIRECTORY_SEPARATOR);

            return $this;
        }

        /**
         * Returns the path to the cache directory.
         * @return string the path to the cache directory
         */
        public function getCacheDirectory()
        {
            return $this->get(self::DIRECTORY_CACHE);
        }

        /**
         * Registers the log directory to the Registry.
         * @param string $logDirectory the log directory to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerLogDirectory($logDirectory)
        {
            TypeValidator::IsString($logDirectory);

            $this->register(self::DIRECTORY_LOG, rtrim($logDirectory, '/\\') . DIRECTORY_SEPARATOR);

            return $this;
        }

        /**
         * Returns the path to the log directory.
         * @return the path to the log directory.
         */
        public function getLogDirectory()
        {
            return $this->get(self::DIRECTORY_LOG);
        }

        /**
         * Returns the registered FrontController instance.
         * @return Brickoo\Library\Http\Interfaces\FrontControllerInterface
         */
        public function getFrontController()
        {
            return $this->get(self::FRONT_CONTROLLER);
        }

        /**
         * Registers the FrontController to the Registry.
         * @param \Brickoo\Library\Http\Interfaces\FrontControllerInterface $FrontController the FrontController to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerFrontController(\Brickoo\Library\Http\Interfaces\FrontControllerInterface $FrontController)
        {
            $this->register(self::FRONT_CONTROLLER, $FrontController);

            return $this;
        }

        /**
         * Returns the registered CacheManager instance.
         * @return \Brickoo\Library\Memory\Interfaces\CacheManagerInterface
         */
        public function getCacheManager()
        {
            return $this->get(self::CACHE_MANAGER);
        }

        /**
         * Registers the default CacheManager to the Registry.
         * @param \Brickoo\Library\Cache\Interfaces\CacheManagerInterface $CacheManager the CacheManager to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerCacheManager(\Brickoo\Library\Cache\Interfaces\CacheManagerInterface $CacheManager)
        {
            $this->register(self::CACHE_MANAGER, $CacheManager);

            return $this;
        }

        /**
         * Retrieves a value from the Registry.
         * @param string|integer $identifier the identifier to retrieve the value from
         * @return mixed the value holded by the identifier
         */
        public function __get($identifier)
        {
            return $this->get($identifier);
        }

        /**
         * Registers a new identifier and add the value to it.
         * @param string|integer $identifier the identifier to register
         * @param string $value the valuue to be holded
         */
        public function __set($identifier, $value)
        {
            $this->register($identifier, $value);
        }

        /**
         * Checks if the identifier is registered.
         * @param string|integer $identifier the identifier to check
         * @return boolean check result
         */
        public function __isset($identifier)
        {
            return $this->isRegistered($identifier);
        }

    }