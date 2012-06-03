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

    namespace Brickoo\Http\Form\Interfaces;

    /**
     * Form
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    Interface Form {

        /**
         * Returns the collected elements.
         * @return array
         */
        public function getElements();

        /**
         * Checks if the elements collection is empty.
         * @return boolean check result
         */
        public function hasElements();

        /**
         * Adds an element to the collection.
         * @param \Brickoo\Http\Form\Element\Interfaces\Element $Element the element to add
         * @return \Brickoo\Http\Form\Builder
         */
        public function addElement(\Brickoo\Http\Form\Element\Interfaces\Element $Element);

        /**
         * Check if the element is in the collection.
         * @param string $elementName the element name to check
         * @return boolean check result
         */
        public function hasElement($elementName);

        /**
         * Removes an element from the collection.
         * @param string $elementName the element name
         * @return \Brickoo\Http\Form\Builder
         */
        public function removeElement($elementName);

        /**
         * Returns the errors amount.
         * @return integer
         */
        public function getErrors();

        /**
         * Checks if the form has errors.
         * @return boolean check result
         */
        public function hasErrors();

        /**
         * Checks if the elements collection is valid.
         * @param array $requestParameters the request parameters to validate
         * @return boolean check result
         */
        public function isValid(array $requestParameters);

    }