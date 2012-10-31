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

    namespace Brickoo\Config\Interfaces;

    /**
     * Configuration
     *
     * Describes a configuration to retrieve and store configuration values.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Configuration {

        /**
         * Loads the configuration through the Provider.
         * @throws Exceptions\ProviderNotAvailableException if no Provider has been set
         * @return \Brickoo\Config\Interfaces\Configuration
         */
        public function load();

        /**
         * Saves the current configuration through the Provider.
         * @throws Exceptions\ProviderNotAvailableException if no Provider has been set
         * @return \Brickoo\Config\Interfaces\Configuration
         */
        public function save();

        /**
        * Converts a configuration entry scalar values to constants.
        * The entry has to be an array, the entry name is used as a prefix.
        * @param string $entry the configuration entry to convert
        * @throws \InvalidArgumentException if the argument is not valid
        * @throws \UnexpectedValueException if the section does not exist
        * @throws \UnexpectedValueException if the value is not scalar
        * @return \Brickoo\Config\Interfaces\Configuration
        */
        public function convertToConstants($entry);

    }