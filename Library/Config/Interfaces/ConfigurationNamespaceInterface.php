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

    namespace Brickoo\Library\Config\Interfaces;

    /**
     * ConfigurationNamespaceInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface ConfigurationNamespaceInterface
    {

        /**
        * Returns the currently reserved namespaces.
        * @return array the reserved namespaces as values
        */
        public static function GetReservedNamespaces();

        /**
         * Checks if the namespace is already reserved.
         * @param string $namespace the namespace to check
         * return boolean check result
         */
        public static function IsNamespaceReserved($namespace);

        /**
         * Returns the namespace working with.
         * @return string the namespace working with
         */
        public function getNamespace();

        /**
         * Checks if the configuration of the identifier is available.
         * @param string $identifier the identifier to check
         * @return boolean check result
         */
        public function hasConfiguration($identifier);

        /**
         * Sets the content to be holded by the identifier.
         * @param string $identifier the identifier to attach the content to
         * @param mixed $content the content to attach to the identifier
         * @return \Brickoo\Library\Config\ConfigurationNamespace
         */
        public function setConfiguration($identifier, $content);

        /**
         * Returns the content holded by the identifier or the default content if the identifier is not available.
         * @param string $identifier the identifier to retrieve the content from
         * @param mixed $defaultValue the default content to return if the identifier is not available
         * @return mixed the identifier holded content otherwise the defaul content
         */
        public function getConfiguration($identifier, $defaultValue = null);

    }