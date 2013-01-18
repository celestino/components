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

    namespace Brickoo\Config\Provider;

    use Brickoo\Validator\Argument;

    /**
     * Implements methods to load and save configuration data.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Ini implements Interfaces\Provider {

        /** @var string */
        private $filename;

        /**
         * Class constructor.
         * @param string $filename the filename of the ini file
         * @return void
         */
        public function __construct($filename) {
             Argument::IsString($filename);
             $this->filename = $filename;
        }

        /** {@inheritDoc} */
        public function load() {
            if (! is_readable($this->filename)
                || (($configuration = @parse_ini_file($this->filename, true)) === false)
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
            return $this->getFlattenEntries($configuration);
        }

        /**
         * Returns a string containing the configuration representation.
         * @param array $entries the configuration entries
         * @param integer $level the recursion level
         * @param string $groupName the group name to create if the key is an integer
         * @return string the flatten configuration
         */
        private function getFlattenEntries(array $entries, $level = 0, $groupName = null) {
            $iniString  = '';

            foreach ($entries as $key => $value) {
                if (is_array($value)) {
                    if ($level == 0) {
                        $iniString .= sprintf("[%s]\r\n", $key);
                    }
                    $iniString .= $this->getFlattenEntries($value, $level+1, $key);
                }
                else {
                    $entryKey = (is_int($key)) ? $groupName.'[]' : $key;
                    $iniString .= sprintf(
                        "%s = ". (ctype_alnum((string)$value) ? "%s" : "\"%s\"") . "\r\n", $entryKey, $value
                    );
                }
            }

            return $iniString;
        }

    }