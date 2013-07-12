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

    namespace Brickoo\Config\Provider;

    use Brickoo\Validator\Argument;

    /**
     * Implements methods to load and save configuration data based on an array.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Standard implements Interfaces\Provider {

        /** @var string */
        private $filename;

        /**
         * Class constructor.
         * @param string $filename the filename of the configuration
         * @return void
         */
        public function __construct($filename) {
            Argument::IsString($filename);
            $this->filename = $filename;
        }

        /** {@inheritDoc} */
        public function load() {
            if ((! is_readable($this->filename))
                || (! $configuration = include($this->filename))
                || (! is_array($configuration))
            ){
                throw new Exceptions\UnableToLoadConfiguration();
            }

            return $configuration;

        }

        /** {@inheritDoc} */
        public function save(array $configuration) {
            $iniData = $this->toString($configuration);

            if (@file_put_contents($this->filename, $iniData) === false) {
                throw new Exceptions\UnableToSaveConfiguration();
            }

            return $this;
        }

        /** {@inheritDoc} */
        public function toString(array $configuration) {
            return "<?php \r\nreturn ". var_export($configuration, true) .";";
        }

    }