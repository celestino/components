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

    namespace Brickoo\Config\Provider;

    use Brickoo\Validator\TypeValidator;

    /**
     * Implements methods to load and save configuration data.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class IniProvider implements Interfaces\ProviderInterface
    {

        /**
         * Holds the ini file name to load or save configuration.
         * @var string
         */
        protected $filename;

        /**
         * Returns the ini filename.
         * @throws UnexpectedValueException if the filename is not set
         * @return string the ini filename
         */
        public function getFilename()
        {
            if ($this->filename === null) {
                throw new \UnexpectedValueException('The filename is `null`.');
            }

            return $this->filename;
        }

        /**
         * Sets the ini filename to use.
         * @param string $filename the ini filename
         * @return \Brickoo\Config\Provider\IniProvider
         */
        public function setFilename($filename)
        {
            TypeValidator::IsString($filename);

            $this->filename = $filename;

            return $this;
        }

        /**
         * Class constructor.
         * Intializes the class properties.
         * Sets the filename containing the configuration
         * @param string $filename the array filename
         * @return void
         */
        public function __construct($filename = null)
        {
            if ($filename !== null) {
                $this->setFilename($filename);
            }
        }

        /**
         * Loads the configuration from an ini file.
         * @param string $filename the file to load the configuration from
         * @throws Exceptions\UnableToLoadConfigurationException if the file does not exist
         * @return array the loaded configuration
         */
        public function load()
        {
            $filename = $this->getFilename();

            if (! is_readable($filename)) {
                throw new Exceptions\UnableToLoadConfigurationException();
            }

            return parse_ini_file($filename, true);

        }

        /**
         * saves the configuration to a file location.
         * @param array $configuration the configuration to save
         * @param string $filename the filename as location
         * @throws Exceptions\UnableToSaveConfigurationException if the configuration could not be saved
         * @return \Brickoo\Config\Provider\IniProvider
         */
        public function save(array $configuration)
        {
            $filename    = $this->getFilename();
            $iniData     = $this->toString($configuration);

            if (@file_put_contents($filename, $iniData) === false) {
                throw new Exceptions\UnableToSaveConfigurationException();
            }

            return $this;
        }

        /**
         * Loads the configuration from a ini format string.
         * @param string $configuration the ini string containing the data
         * @return array the loaded configuration
         */
        public function fromString($configuration)
        {
            TypeValidator::IsString($configuration);

            return parse_ini_string($configuration, true);
        }

        /**
         * Converts the configuration to an ini string.
         * @param array $configuration the configuration to convert
         * @return string the configuration as an ini format
         */
        public function toString(array $configuration)
        {
            $iniString = '';

            foreach($configuration as $section => $settings) {
                if (is_array($settings)) {
                    $iniString .= $this->getSectionString($section, $settings);
                }
            }

            return $iniString;
        }

        /**
         * Returns a string containing the section configuration.
         * @param string $section the section name
         * @param array $settings the section settings
         * @return string the section configuration
         */
        public function getSectionString($section, array $settings)
        {
            TypeValidator::IsString($section);

            $iniString = sprintf("[%s]\r\n", $section);

            foreach ($settings as $key => $value) {
                if (is_array($value)) {
                    $iniString .= $this->getSectionArrayString($key, $value);
                }
                else {
                    $iniString .= sprintf("%s = ". (ctype_alnum((string)$value) ? "%s" : "\"%s\"") . "\r\n", $key, $value);
                }
            }

            return $iniString;
        }

        /**
         * Returns a string containing the key/value pairs
         * @param string $key the key name
         * @param array $values the values of the key
         * @return string the section array configuration
         */
        public function getSectionArrayString($key, array $values)
        {
            TypeValidator::IsString($key);

            $iniString = '';

            foreach ($values as $value) {
                $iniString .= sprintf("%s[] = ". (ctype_alnum((string)$value) ? "%s" : "\"%s\"") . "\r\n", $key, $value);
            }

            return $iniString;
        }

    }