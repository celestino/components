<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Brickoo\Session\Interfaces;

    /**
     * Container
     *
     * Describes a session container for holding session values.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Container {

        /**
        * Checks if the session property is available.
        * @param string $property the property to check in the session
        * @return boolean check result
        */
        public function has($property);

        /**
         * Returns the session property holded content or the default value.
         * @param string $property the session property to retrieve the content from
         * @param mixed $defaultValue the default value if the property does not exist
         * @return mixed the property holded content or the default value if the property does not exist
         */
        public function get($property, $defaultValue = null);

        /**
         * Sets the session property and assigns the content to it.
         * @param string $property the property to assign the content to
         * @param mixed $content the content to store
         * @return \Brickoo\Http\Session\Interfaces\Container
         */
        public function set($property, $content);

        /**
         * Removes the session property if available.
         * @param string $property the property to remove
         * @return \Brickoo\Http\Session\Interfaces\Container
         */
        public function remove($property);

    }