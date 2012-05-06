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

    namespace Brickoo\Module\Component;

    use Brickoo\Validator\TypeValidator;

    /**
     * GenericInformation
     *
     * Used to describe a module information value.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class GenericInformation implements Interfaces\Information {

        /**
         * Holds the name of the description information.
         * @var string
         */
        protected $name;

        /**
         * Sets the name of the information.
         * @param string $name the unique information name
         * @return \Brickoo\Module\Component\GenericInformation
         */
        protected function setName($name) {
            TypeValidator::IsStringAndNotEmpty($name);

            $this->name = strtolower($name);
            return $this;
        }

        /**
         * Returns the information name.
         * @returns string the information name
         */
        public function getName() {
            return $this->name;
        }

        /**
         * Holds the value of the module information.
         * @var string
         */
        protected $value;

        /**
         * Returns the module information value.
         * @throws \UnexpectedValueException if the module information is not set and requiried
         * @return string the module information value
         */
        public function get() {
            if ($this->value === null) {
                throw new \UnexpectedValueException(sprintf("The information `%s` is `null`.", $this->name));
            }

            return $this->value;
        }

        /**
         * Sets the module information value.
         * @param mixed $value the module information value to set
         * @return \Brickoo\Module\Description
         */
        public function set($value) {
            $this->value = $value;
            return $this;
        }

        /**
         * Class constructor.
         * Initialized the class property.
         * @param string $name the information name
         * @param mixed $value the information value
         * @return void
         */
        public function __construct($name, $value = null) {
            $this->setName($name)->set($value);
        }

        /**
        * Returns the information value as string.
        * @return string the information value
        */
        public function toString() {
            return (string)$this->get();
        }

        /**
         * Returns the string represantion of the description information.
         * @return string the description information
         */
        public function __toString() {
            return $this->toString();
        }

    }