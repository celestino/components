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

    namespace Brickoo\Http\Form\Converter;

    /**
     * ToArray
     *
     * Transforms a form and its elements to a simple array representation.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ToArray implements Interfaces\Converter {

        /**
         * Holds the form container errors key.
         * @var string
         */
        const FORM_CONTAINER_ERRORS = 'errors';

        /**
         * Holds the elements container key of the form.
         * @var string
         */
        const FORM_CONTAINER_ELEMENTS = 'elements';

        /**
         * Holds the element container key for the error messages.
         * @var string
         */
        const ELEMENT_CONTAINER_ERROR_MESSAGES = 'messages';

        /**
         * Holds the elements container key for the element  value.
         * @var string
         */
        const ELEMENT_CONTAINER_VALUE = 'value';

        /**
         * Holds the elements container key for the element name.
         * @var string
         */
        const ELEMENT_CONTAINER_NAME = 'name';

        /**
         * Holds the elements container key for the requirement flag.
         * @var unknown_type
         */
        const ELEMENT_CONTAINER_REQUIRED = 'required';

        /**
         * Returns the converted form containing the elements and their values.
         * @param \Brickoo\Http\Form\Interfaces\Form $Form the Form instance to convert
         * @return array the form converted array representation
         */
        public function convert(\Brickoo\Http\Form\Interfaces\Form $Form) {
            $formContainer = $this->getFormContainer($Form);

            if ($Form->hasElements()) {
                $Elements = $Form->getElements();
                foreach ($Elements as $index => $Element) {
                    $formContainer[self::FORM_CONTAINER_ELEMENTS][$index] = $this->getElementContainer($Element);
                }
            }

            return $formContainer;
        }

        /**
         * Returns the form container with the main structure.
         * @param \Brickoo\Http\Form\Interfaces\Form $Form the Form dependency to return the cotainer from
         * @return array the form structure container
         */
        public function getFormContainer(\Brickoo\Http\Form\Interfaces\Form $Form) {
            return array(
                self::FORM_CONTAINER_ERRORS      => $Form->getErrors(),
                self::FORM_CONTAINER_ELEMENTS    => array()
            );
        }

        /**
         * Returns the element container representing the element structure.
         * @param \Brickoo\Http\Form\Element\Interfaces\Element $Element the element to return the structure from
         * @return array the element container representation
         */
        public function getElementContainer(\Brickoo\Http\Form\Element\Interfaces\Element $Element) {
           return array(
               self::ELEMENT_CONTAINER_REQUIRED          => $Element->isRequired(),
               self::ELEMENT_CONTAINER_NAME              => $Element->getName(),
               self::ELEMENT_CONTAINER_VALUE             => $Element->getValue(),
               self::ELEMENT_CONTAINER_ERROR_MESSAGES    => $Element->getErrorMessages()
           );
        }

    }