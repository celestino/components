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

    namespace Brickoo\Module;

    use Brickoo\Core,
        Brickoo\Memory,
        Brickoo\Validator\TypeValidator;

    /**
     * Description
     *
     * Implements methods to describe a module.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Description implements Interfaces\Description {

        /**
         * Holds all added information instances.
         * @var \Brickoo\Memory\Container
         */
        protected $InformationCollection;

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct(\Brickoo\Memory\Interfaces\Container $Container = null) {
            if ($Container === null) {
                $Container = new Memory\Container();
            }
            $this->InformationCollection = $Container;
        }

        /**
         * Adds a module information element to the collection.
         * @param \Brickoo\Module\Component\Interfaces\Information $Informationthe information to add
         * @throws Core\Exceptions\ValueOverwriteException if trying to overwritte an information with the same name
         * @return \Brickoo\Module\Description
         */
        public function add(\Brickoo\Module\Component\Interfaces\Information $Information) {
            if ($this->InformationCollection->has($infoName = $Information->getName())) {
                throw new Core\Exceptions\ValueOverwriteException(sprintf("Information::", $infoName));
            }
            $this->InformationCollection->set($infoName, $Information);

            return $this;
        }

        /**
         * Checks if the information with a specific name is set.
         * @param string $informationName the information name to check
         * @return boolean check result
         */
        public function has($informationName) {
            TypeValidator::IsString($informationName);

            return $this->InformationCollection->has($informationName);
        }

        /**
         * Returns the information value of the specific name.
         * @param string $informationName the information name to retrieve the value from
         * @throws \UnexpectedValueException
         * @return string the information value
         */
        public function get($informationName) {
            TypeValidator::IsString($informationName);

            if (! $Information = $this->InformationCollection->get($informationName)) {
                throw new \UnexpectedValueException(sprintf("The information `%s` is not set.", $informationName));
            }

            return $Information->get();
        }

        /**
         * Returns all collected information objects.
         * @return array all collected information objects
         */
        public function getAll() {
            return $this->InformationCollection->toArray();
        }

        /**
         * Returns a string represantation of the collected informations.
         * @return string the string information from all collected information objects
         */
        public function toString() {
            $information = '';

            $this->InformationCollection->rewind();
            while ($this->InformationCollection->valid()) {
                $information .= sprintf("%s : %s\r\n",
                    $this->InformationCollection->current()->getName(),
                    $this->InformationCollection->current()->toString()
                );
                $this->InformationCollection->next();
            }

            return $information;
        }

        /**
         * Returns the collected information as string.
         * @return string the collected information
         */
        public function __toString() {
            return $this->toString();
        }

    }