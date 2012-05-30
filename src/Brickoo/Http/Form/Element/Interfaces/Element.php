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

    namespace Brickoo\Http\Form\Element\Interfaces;

    /**
     * Element
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Element {

        /**
         * Returns the element name.
         * @return string the element name
         */
        public function getName();

        /**
         * Returns the element value.
         * @return mixed the element value
         */
        public function getValue();

        /**
         * Checks if the element has a value.
         * @return boolean check result
         */
        public function hasValue();

        /**
         * Checks if the element is required.
         * @return boolean check result
         */
        public function isRequired();

        /**
         * Returns the errors occured.
         * @return array the error messages
         */
        public function getErrorMessages();

        /**
         * Checks if the element has error messages.
         * @return boolean check result
         */
        public function hasErrorMessages();

        /**
         * Adds a validator to the element.
         * @param callable $Validator the validator to add
         * @param string $errorMessage the error message of this validator on failure
         * @return \Brickoo\Http\Form\Interfaces\Element
         */
        public function addValidator($Validator, $errorMessage);

        /**
         * Checks if the element is valid with the current request data.
         * @param array $requestParameters the request parameters to extract the field from
         * @return boolean validation check result
         */
        public function isValid(array $requestParameters);

        /**
         * Returns the filtered valued.
         * This method should be overridden to implement an own filter algorithm.
         * @param mixed the value to filter with the element algorythm
         * @return mixed the filtered element value
         */
        public function filter($value);

    }