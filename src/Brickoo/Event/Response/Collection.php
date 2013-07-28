<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Brickoo\Event\Response;

    /**
     * Collection
     *
     * Implements a collection containing a collection of event listeners responses.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Collection implements Interfaces\Collection {

        /** @var array */
        private $responsesContainer;

        /**
         * Class constructor.
         * @param array $responsesContainer the listeners responses
         * @return void
         */
        public function __construct(array $responsesContainer) {
            $this->responsesContainer = $responsesContainer;
        }

        /** {@inheritDoc} */
        public function shift() {
            if ($this->isEmpty()) {
                throw new Exceptions\ResponseNotAvailable();
            }
            return array_shift($this->responsesContainer);
        }

        /** {@inheritDoc} */
        public function pop() {
            if ($this->isEmpty()) {
                throw new Exceptions\ResponseNotAvailable();
            }
            return array_pop($this->responsesContainer);
        }

        /** {@inheritDoc} */
        public function getAll() {
            if ($this->isEmpty()) {
                throw new Exceptions\ResponseNotAvailable();
            }
            return $this->responsesContainer;
        }

        /** {@inheritDoc} */
        public function isEmpty() {
            return empty($this->responsesContainer);
        }

        /** {@inheritDoc} */
        public function count() {
            return count($this->responsesContainer);
        }

    }