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

    use Brickoo\Library\Storage;

    /**
     * Brickoo framework main class.
     * Used to have an global Registry which is part managed by the framework.     *
     * Holds an object of the \Brickoo\Library\Storage\Registry.
     * Defines the Registry identifers reserved.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Brickoo implements Interfaces\BrickooInterface
    {

        /**
         * Defines the BrickOO Version.
         * @var string
         */
        const VERSION = '3.0-DEV';

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
        const DIRECTORY_LOGS        = 'brickoo.directory.logs';
        const DIRECTORY_CACHE       = 'bricko.directory.cache';
        const MODULES               = 'brickoo.modules';
        const ENVIRONMENT           = 'brickoo.environment';
        const AUTOLOADER            = 'brickoo.autoloader';
        const REQUEST               = 'brickoo.request';
        const LOGGER                = 'brickoo.logger';
        const ERROR_HANDLER         = 'brickoo.error.handler';
        const EXCEPTION_HANDLER     = 'brickoo.exception.handler';
        const CACHE_MANAGER         = 'brickoo.cache.manager';
        const ROUTER                = 'brickoo.router';
        const FRONT_CONTROLLER      = 'brickoo.front.controller';

        /**
         * Holds an instance of the Registry class.
         * @var \Brickoo\Library\Storage\Interfaces\RegistryInterface
         */
        protected static $Registry;

        /**
         * Lazy initialization of the Registry dependency.
         * Returns the holded Registry instance.
         * @return \Brickoo\Library\Storage\Interfaces\RegistryInterface
         */
        public function getRegistry()
        {
            if (static::$Registry === null)
            {
                $this->injectRegistry(new Storage\Registry());
            }

            return static::$Registry;
        }

        /**
         * Injects the Registry dependency to use.
         * @param Brickoo\Library\Storage\Interfaces\RegistryInterface $Registry the Registry dependency
         * @throws DependencyOverwriteException if trying to override dependency
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function injectRegistry(\Brickoo\Library\Storage\Interfaces\RegistryInterface $Registry)
        {
            if (static::$Registry !== null)
            {
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
            $this->getRegistry()->register($identifier, $value);
            $this->getRegistry()->lock($identifier);

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
         * Checks if a module has been registered and should be available.
         * @param unknown_type $moduleName the module name to check
         * @return boolean check result
         */
        public function isModuleAvailable($moduleName)
        {
            if (! $this->getRegistry()->isRegistered(self::MODULES))
            {
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
            if (! $this->isModuleAvailable($moduleName))
            {
                throw new Exceptions\ModuleNotAvailableException($moduleName);
            }

            $modules = $this->getRegistry()->getRegistered(self::MODULES);

            return $modules[$moduleName];
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
         * @return \Brickoo\Library\Core\Brickoo
         */
        public function __set($identifier, $value)
        {
            return $this->register($identifier, $value);
        }

    }

?>