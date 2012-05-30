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

    namespace Brickoo\Http\Form;

    use Brickoo\Core,
        Brickoo\Memory,
        Brickoo\Validator\TypeValidator;

    /**
     * Builder
     *
     * Simple form builder for adding elements to validate values against.
     * @todo: Add a simple render engine to return a represantation of the html form with the contained elements.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SimpleForm {

        /**
         * Holds a collection of form elements.
         * @var \Brickoo\Memory\Container
         */
        protected $Elements;

        /**
         * Returns the collected elements.
         * @return array
         */
        public function getElements() {
            return $this->Elements->toArray();
        }

        /**
         * Checks if the elements collection is empty.
         * @return boolean check result
         */
        public function hasElements() {
            return (! $this->Elements->isEmpty());
        }

        /**
         * Adds an element to the collection.
         * @param \Brickoo\Http\Form\Element\Interfaces\Element $Element the element to add
         * @return \Brickoo\Http\Form\Builder
         */
        public function addElement(\Brickoo\Http\Form\Element\Interfaces\Element $Element) {
            if ($this->Elements->has(($elementName = $Element->getName()))) {
                throw new Core\Exceptions\ValueOverwriteException(sprintf("Element::%s", $elementName));
            }

            $this->Elements->set($elementName, $Element);
            return $this;
        }

        /**
         * Check if the element is in the collection.
         * @param string $elementName the element name to check
         * @return boolean check result
         */
        public function hasElement($elementName) {
            TypeValidator::IsStringAndNotEmpty($elementName);
            return $this->Elements->has($elementName);
        }

        /**
         * Removes an element from the collection.
         * @param string $elementName the element name
         * @return \Brickoo\Http\Form\Builder
         */
        public function removeElement($elementName) {
            TypeValidator::IsStringAndNotEmpty($elementName);

            if (! $this->Elements->has($elementName)) {
                throw new Exceptions\ElementNotAvailableException($elementName);
            }

            $this->Elements->delete($elementName);
            return $this;
        }

        /**
         * Holds the errors amount.
         * @var integer
         */
        protected $errors;

        /**
         * Returns the errors amount.
         * @return integer
         */
        public function getErrors() {
            return $this->errors;
        }

        /**
         * Checks if the form has errors.
         * @return boolean check result
         */
        public function hasErrors() {
            return ($this->errors !== 0);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param \Brickoo\Memory\Interfaces\Container $Container the dependency to inject
         * @return void
         */
        public function __construct(\Brickoo\Memory\Interfaces\Container $Container = null) {
            if ($Container === null) {
                $Container =  new Memory\Container();
            }

            $this->Elements          = $Container;
            $this->errors            = 0;
        }

        /**
         * Checks if the elements collection is valid.
         * @param array $requestParameters the request parameters to validate
         * @return boolean check result
         */
        public function isValid(array $requestParameters) {
            if ($this->hasErrors()){
                return false;
            }

            $this->Elements->rewind();
            while ($this->Elements->valid()) {
                $Element = $this->Elements->current();
                if (! $Element->isValid($requestParameters)) {
                    $errorMessages = $Element->getErrorMessages();
                    $this->errorMessages[$Element->getName()] = (array)$errorMessages;
                    $this->errors ++;
                }
                $this->Elements->next();
            }

            return (! $this->hasErrors());
        }

        /**
         * Returns the error messages from the validation elements.
         * @return array the error messages
         */
        public function getElementsErrorMessages() {
            if (! $this->hasErrors()) {
                return array();
            }

            $errorMessages = array();

            $this->Elements->rewind();
            while ($this->Elements->valid()) {
                $Element = $this->Elements->current();
                if ($Element->hasErrorMessages()) {
                    $errorMessages[$Element->getName()] = $Element->getErrorMessages();
                }
                $this->Elements->next();
            }

            return $errorMessages;
        }

        /**
         * Returns the validation elements values as name/value pairs.
         * @return return the validation elements values
         */
        public function getElementsValues() {
            $elementsValues = array();

            $this->Elements->rewind();
            while ($this->Elements->valid()) {
                $Element = $this->Elements->current();
                if ($Element->hasValue()) {
                    $elementsValues[$Element->getName()] = $Element->getValue();
                }
                $this->Elements->next();
            }

            return $elementsValues;
        }

    }