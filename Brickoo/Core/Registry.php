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

    use Brickoo\Memory;
    use Brickoo\Validator\TypeValidator;

    /**
     * Registry
     *
     * Implements a Registry which holds a Memory\Registry that does not allow to be overwritten.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Registry implements Interfaces\RegistryInterface
    {

        /**
         * Holds an instance of the Registry class.
         * @var \Brickoo\Memory\Interfaces\RegistryInterface
         */
        protected static $_Registry;

        /**
         * Lazy initialization of the Registry dependency.
         * @param \Brickoo\Memory\Interfaces\RegistryInterface $Registry the Registry dependency
         * @throws DependencyOverwriteException if trying to override the Registry dependency available
         * @return \Brickoo\Memory\Interfaces\RegistryInterface
         */
        public function Registry(\Brickoo\Memory\Interfaces\RegistryInterface $Registry = null)
        {
            if ($Registry !== null) {
                if (static::$_Registry !== null) {
                    throw new Exceptions\DependencyOverwriteException('RegistryInterface');
                }
                static::$_Registry = $Registry;
                return $this;
            }

            if (static::$_Registry === null) {
                static::$_Registry = new Memory\Registry();
            }

            return static::$_Registry;
        }

        /**
         * Shortcut to retrieve a value from the Registry.
         * @param string|integer $identifier the identifier to retrieve the value from
         * @return mixed the value holded by the identifier
         */
        public function get($identifier)
        {
            return $this->Registry()->get($identifier);
        }

        /**
         * Shortcut to register an new identifier and add the value to it.
         * This method also locks(!) the identifier, since the registry should
         * not allow to overwrite a system wide available identifier.
         * @param string|integer $identifier the identifier to register
         * @param string $value the valuue to be holded
         * @return \Brickoo\Core\Registry
         */
        public  function register($identifier, $value)
        {
            $this->Registry()->register($identifier, $value)->lock($identifier);

            return $this;
        }

        /**
         * Shorcut to check if an identifier is registered.
         * @param string|integer $identifier the identifier to check its availability
         * @return boolean check result
         */
        public function isRegistered($identifier)
        {
            return isset($this->Registry()->$identifier);
        }

    }