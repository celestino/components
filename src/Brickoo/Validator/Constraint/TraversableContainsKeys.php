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

    namespace Brickoo\Validator\Constraint;

    use Brickoo\Validator\Argument;

    /**
     * TraversableContainsKeys
     *
     * Asserts that an array or traversable contains all expected keys.
     * Does not have any effect if the traversable also contains other keys.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class TraversableContainsKeys implements Interfaces\Constraint {

        /** @var array */
        private $expectedKeys;

        /**
         * Class constructor.
         * @param array $expectedKeys the expected keys
         * @throws \InvalidArgumentException if an argument is not valid.
         * @return void
         */
        public function __construct(array $expectedKeys) {
            $this->expectedKeys = $expectedKeys;
        }


        /**
         * {@inheritDoc}
         * @param array|Traversable $compareFrom the traversable to compare the keys from
         */
        public function assert($compareFrom) {
            Argument::IsTraversable($compareFrom);

            $result = array_diff($this->expectedKeys, $this->getTraversableKeys($compareFrom));
            return empty($result);
        }

        /**
         * Returns the traversable contained keys as values.
         * @param array|Traversable $compareFrom the traversable to return the keys from
         * @return array the array keys as values
         */
        private function getTraversableKeys($compareFrom) {
            if (is_array($compareFrom)) {
                return array_keys($compareFrom);
            }

            return array_keys(iterator_to_array($compareFrom, true));
        }

    }