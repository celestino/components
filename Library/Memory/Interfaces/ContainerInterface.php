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

    namespace Brickoo\Library\Memory\Interfaces;

    /**
     * ContainerInterface
     *
     * Descripes the extended interfaces and mehtods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface ContainerInterface extends \ArrayAccess, \Iterator, \Countable
    {

        /**
        * Returns the value of the given offset.
        * @param string|integer $offset the offset to retrieve the value from
        * @param mixed $defaultValue the default value if the offset does not exist
        * @return mixed the offset contained value or the default value passed
        */
        public function get($offset, $defaultValue = null);

        /**
         * Adds an offset-value pair to the array object.
         * @param string|imteger $offset the offset to add
         * @param mixed $value the value of the offset
         * @return \Brickoo\Library\Memory\Container
         */
        public function add($offset, $value);

        /**
         * Checks if the element is available.
         * @param string|integer $offset the element to check
         * @return boolean check result
         */
        public function has($offset);

        /**
         * Merges the passed contianer with the currently holded.
         * @param array $container the container to merge
         * @return \Brickoo\Library\Memory\Container
         */
        public function merge(array $container);

        /**
         * Checks if any value are assigned.
         * @return boolean check result
         */
        public function isEmpty();

    }