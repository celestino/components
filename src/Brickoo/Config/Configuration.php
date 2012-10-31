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

    namespace Brickoo\Config;

    use Brickoo\Memory,
        Brickoo\Validator\Argument;

    /**
     * Configuration
     *
     * Implements a configuration to store and retrieve configuration values.
     * Uses the \Brickoo\Memory\Container to store the configuration values.
     * @see \Brickoo\Memory\Container
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Configuration extends Memory\Container implements Interfaces\Configuration {

        /** @var \Brickoo\Config\Provider\Interfaces\Provider */
        protected $Provider;

        /**
         * Class constructor.
         * @param \Brickoo\Config\Provider\Interfaces\Provider $Provider
         * @return void
         */
        public function __construct(\Brickoo\Config\Provider\Interfaces\Provider $Provider) {
            parent::__construct();
            $this->Provider = $Provider;
        }

        /** {@inheritDoc} */
        public function load() {
            $this->fromArray($this->Provider->load());
            return $this;
        }

        /** {@inheritDoc} */
        public function save() {
            $this->Provider->save($this->toArray());
            return $this;
        }

        /** {@inheritDoc} */
        public function convertToConstants($entry) {
            Argument::IsString($entry);

            if (($settings = $this->get($entry)) === null) {
                throw new \UnexpectedValueException(sprintf("The entry `%s` does not exist.", $entry));
            }

            foreach($settings as $key => $value) {
                if (! is_scalar($value)) {
                    throw new \UnexpectedValueException(sprintf("The value of `%s[%s]` is not scalar.", $entry, $key));
                }

                $constKey = strtoupper($entry ."_". $key);

                if (! defined($constKey)) {
                    define($constKey, $value);
                }
            }

            return $this;
        }

    }