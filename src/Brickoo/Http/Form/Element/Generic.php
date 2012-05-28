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

    namespace Brickoo\Http\Form\Element;

    use Brickoo\Validator\TypeValidator;

    /**
     * Generic
     *
     * A generic element to validate a form input.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     *
     */
    class Generic implements Interfaces\Element {

        /**
         * The validators storage fields.
         * @var string
         */
        const CALLBACK_FIELD    = 'callback';
        const MESSAGE_FIELD     = 'message';

        /**
         * Holds the name of the element.
         * @var string
         */
        protected $name;

        /**
         * Returns the element name.
         * @return string the element name
         */
        public function getName() {
            return $this->name;
        }

        /**
         * Holds the element value.
         * @var string|integer
         */
        protected $value;

        /**
         * Returns the element value.
         * @return mixed the element value
         */
        public function getValue() {
            return $this->value;
        }

        /**
         * Checks if the element has a value.
         * @return boolean check result
         */
        public function hasValue() {
            return ($this->value !== null);
        }

        /**
         * Holds the flag to mark the element as required.
         * @var boolean
         */
        protected $required;

        /**
         * Sets the required element flag.
         * @param boolean $required the required flag
         * @return \Brickoo\Http\Form\Element\Generic
         */
        public function setRequired($required) {
            TypeValidator::IsBoolean($required);

            $this->required = $required;
            return $this;
        }

        /**
         * Checks if the element is required.
         * @return boolean check result
         */
        public function isRequired() {
            return $this->required;
        }

        /**
         * Holds the error messages occured.
         * @var array
         */
        protected $errorMessages;

        /**
         * Returns the errors occured.
         * @return array the error messages
         */
        public function getErrorMessages() {
            return $this->errorMessages;
        }

        /**
         * Checks if the element has error messages.
         * @return boolean check result
         */
        public function hasErrorMessages() {
            return (count($this->errorMessages) > 0);
        }

        /**
         * Holds the element validators list.
         * @var array
         */
        protected $validators;

        /**
         * Adds a validator to the element.
         * @param callable $Validator the validator to add
         * @param string $errorMessage the error message of this validator on failure
         * @return \Brickoo\Http\Form\Element\Generic
         */
        public function addValidator($Validator, $errorMessage) {
            TypeValidator::IsStringAndNotEmpty($errorMessage);
            TypeValidator::IsCallable($Validator);

            $this->validators[] = array(
                self::CALLBACK_FIELD => $Validator, self::MESSAGE_FIELD => $errorMessage
            );

            return $this;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param string $name the element name
         * @param mixed $defaultValue the element default value
         * @param boolean $required flag to make the field required
         */
        public function __construct($name, $defaultValue = null, $required = true) {
            TypeValidator::IsStringAndNotEmpty($name);
            TypeValidator::IsBoolean($required);

            $this->name          = $name;
            $this->value         = $defaultValue;
            $this->required      = $required;
            $this->validators    = array();
        }

        /**
         * Checks if the element is valid with the current request data.
         * @param array $requestParameters the request parameters to extract the field from
         * @return boolean validation check result
         */
        public function isValid(array $requestParameters) {
            if (($requestValue = $this->getRequestValue($requestParameters)) === null) {
                return ($this->isRequired() ? false : true);
            }

            foreach ($this->validators as $validator) {
                if (! $valid = (boolean)call_user_func_array(
                    $validator[self::CALLBACK_FIELD], array($this->getName(), $requestValue, $requestParameters)
                )){
                    $this->errorMessages[] = $validator[self::MESSAGE_FIELD];
                }
            }

            if ($this->hasErrorMessages()) {
                return false;
            }

            $this->value = $this->filter($requestValue);

            return true;
        }

        /**
         * Returns the value to validate from the request parameters.
         * @param array $requestParameters the requestParameters
         * @return string the request value or null if not available in the request parameters
         */
        public function getRequestValue(array $requestParameters) {
            return (isset($requestParameters[$this->getName()]) ? $requestParameters[$this->getName()] : null);
        }

        /**
         * Returns the filtered valued.
         * This method should be overridden to implement an own filter algorithm.
         * @param mixed the value to filter with the element algorythm
         * @return mixed the filtered element value
         */
        public function filter($value) {
            return $value;
        }
    }
