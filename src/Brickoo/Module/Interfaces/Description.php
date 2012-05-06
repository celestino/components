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

    namespace Brickoo\Module\Interfaces;

    /**
     * Description
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Description {

        /**
         * Adds a module information element to the collection.
         * @param \Brickoo\Module\Component\Interfaces\Information $Informationthe information to add
         * @throws Core\Exceptions\ValueOverwriteException if trying to overwritte an information with the same name
         * @return \Brickoo\Module\Interfaces\Description
         */
        public function add(\Brickoo\Module\Component\Interfaces\Information $Information);

        /**
         * Checks if the information with a specific name is set.
         * @param string $informationName the information name to check
         * @return boolean check result
         */
        public function has($informationName);

        /**
         * Returns the information value of the specific name.
         * @param string $informationName the information name to retrieve the value from
         * @throws \UnexpectedValueException
         * @return string the information value
         */
        public function get($informationName);

        /**
         * Returns all collected information objects.
         * @return array all collected information objects
         */
        public function getAll();

        /**
         * Returns a string represantation of the collected informations.
         * @return string the string information from all collected information objects
         */
        public function toString();

    }