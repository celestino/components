<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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
     * RequestInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    Interface RequestInterface
    {

        /**
         * Returns the Cli Request object.
         * If the object does not exist, it wll be created.
         * @return object Cli implementing the RequestInterface
         */
        public function Cli();

        /**
         * Lazy initialization of the Cli Request instance.
         * @param Cli\Interfaces\RequestInterface $Cli the Cli object implementing the RequestInterface
         * @return object reference
         */
        public function injectCliRequest(\Brickoo\Library\Cli\Interfaces\RequestInterface $Cli);

        /**
         * Returns the Http Request object.
         * If the object does not exist, it wll be created.
         * @return object Cli implementing the RequestInterface
         */
        public function Http();

        /**
         * Lazy initialization of the Http Request instance.
         * @param Http\Interfaces\RequestInterface $Http the Http object implementing the RequestInterface
         * @throws DependencyOverrideException if trying to override the dependency
         * @return object reference
         */
        public function injectHttpRequest(\Brickoo\Library\Http\Interfaces\RequestInterface $Http);

        /**
         * Returns the server variable value of the passed key.
         * @param string $keyName the key name to return the value from
         * @param mixed $defaultValue the default value to return if unset
         * @throws InvalidArgumentException if the key or apache flag is not valid
         * @return string/mixed the header or default value
         */
        public function getServerVar($keyName, $defaultValue = null);

        /**
         * Checks if the passed interface is used.
         * @param string $interface the interface to check
         * @return boolean check result
         */
        public function isPHPInterface($interface);

        /**
         * Clears the request object properties.
         * @return object reference
         */
        public function clear();

    }

?>