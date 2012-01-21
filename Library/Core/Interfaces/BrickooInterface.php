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
         * @return \Brickoo\Library\Storage\Interfaces\RegistryInterface
         */
        public function getRegistry();

        /**
         * Injects the Registry dependency to use.
         * @param Brickoo\Library\Storage\Interfaces\RegistryInterface $Registry the Registry dependency
         * @throws DependencyOverwriteException if trying to override dependency
         * @return Brickoo\Library\Core\Brickoo
         */
        public function injectRegistry(\Brickoo\Library\Storage\Interfaces\RegistryInterface $Registry);

        /**
         * This is an alias for the Brickoo::getRegistry method.
         * @return \Brickoo\Library\Storage\Interfaces\RegistryInterface
         */
        public function Reg();

        /**
         * Shortcut to retrieve a value from the Registry.
         * @param string|integer $identifier the identifier to retrieve the value from
         * @return mixed the value holded by the identifier
         */
        public function get($identifier);

        /**
         * Shortcut to register a new identifier and add the value to it.
         * This mehtod also locks(!) the identifier, since the registry should
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

    }

?>