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

    namespace Brickoo\Library\Core\Interfaces;

    /**
     * AutoloaderInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface BrickooInterface
    {

        /**
         * Lazy initialization of the Registry dependency.
         * Returns the holded Registry instance.
         * @return \Brickoo\Library\Memory\Interfaces\RegistryInterface
         */
        public function getRegistry();

        /**
         * Injects the Registry dependency to use.
         * @param Brickoo\Library\Memory\Interfaces\RegistryInterface $Registry the Registry dependency
         * @throws DependencyOverwriteException if trying to override dependency
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function injectRegistry(\Brickoo\Library\Memory\Interfaces\RegistryInterface $Registry);

        /**
         * Shortcut to retrieve a value from the Registry.
         * @param string|integer $identifier the identifier to retrieve the value from
         * @return mixed the value holded by the identifier
         */
        public function get($identifier);

        /**
         * Shortcut to register a new identifier and add the value to it.
         * This method also locks(!) the identifier, since the registry should
         * not allow to overwrite an registered system wide identifier.
         * @param string|integer $identifier the identifier to register
         * @param string $value the valuue to be holded
         * @return \Brickoo\Library\Core\Brickoo
         */
        public  function register($identifier, $value);

        /**
         * Shorcut to check if an identifier is registered.
         * @param string|integer $identifier the identifier to check its availability
         * @return boolean check result
         */
        public function isRegistered($identifier);

        /**
        * Returns the full BrickOO version with prefix and suffix.
         * @return string the full BrickOO version
        */
        public function getVersion();

        /**
        * Returns the BrickOO version number without prefix or suffix.
        * @return string the BrickOO version number
        */
        public function getVersionNumber();

        /**
         * Registers the Autoloader to the Registry.
         * @param \Brickoo\Library\Core\Interfaces\AutoloaderInterface $Autoloader the Autoloader to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerAutoloader(\Brickoo\Library\Core\Interfaces\AutoloaderInterface $Autoloader);

        /**
         * Registers the available modules to the Registry.
         * @param array $modules the available modules to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerModules(array $modules);

        /**
         * Returns the available modules.
         * @return array the available modules
         */
        public function getModules();

        /**
         * Checks if a module has been registered and should be available.
         * @param unknown_type $moduleName the module name to check
         * @return boolean check result
         */
        public function isModuleAvailable($moduleName);

        /**
         * Returns the module absolute path to the root directory.
         * @param unknown_type $moduleName the module to return the path from
         * @throws Exceptions\ModuleNotAvailableException if the module is not available
         * @return string the module absolute path to the root directory
         */
        public function getModulePath($moduleName);

        /**
         * Registers the current environment to the Registry.
         * @param integer $environment the environment currently used
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerEnvironment($environment);

        /**
         * Checks if the environment is currently used.
         * @param integer $environment the environment to check
         * @return boolean check result
         */
        public function isEnvironment($environment);

        /**
         * Registers the cache directory to the Registry.
         * @param string $cacheDirectory the cache directory to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerCacheDirectory($cacheDirectory);

        /**
         * Returns the path to the cache directory.
         * @return string the path to the cache directory
         */
        public function getCacheDirectory();

        /**
         * Registers the log directory to the Registry.
         * @param string $logDirectory the log directory to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerLogDirectory($logDirectory);

        /**
         * Returns the path to the log directory.
         * @return the path to the log directory.
         */
        public function getLogDirectory();

        /**
         * Registers the FrontController to the Registry.
         * @param \Brickoo\Library\Http\Interfaces\FrontControllerInterface $FrontController the FrontController to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerFrontController(\Brickoo\Library\Http\Interfaces\FrontControllerInterface $FrontController);

        /**
         * Registers the default CacheManager to the Registry.
         * @param \Brickoo\Library\Cache\Interfaces\CacheManagerInterface $CacheManager the CacheManager to register
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function registerCacheManager(\Brickoo\Library\Cache\Interfaces\CacheManagerInterface $CacheManager);

    }