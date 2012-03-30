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
        Brickoo\Validator\TypeValidator;

    /**
     * Configuration
     *
     * Implements methods to store and retrieve configuration values.
     * Uses the Memory\Container for configuration storage.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Configuration extends Memory\Container implements Interfaces\ConfigurationInterface
    {

        /**
         * Holds an instance implementing the ProviderInterface.
         * @var \Brickoo\Config\Provider\Interfaces\ProviderInterface
         */
        protected $_Provider;

        /**
         * Returns the configuration Provider.
         * @return \Brickoo\Config\Provider\Interfaces\ProviderInterface
         */
        public function Provider()
        {
            return $this->_Provider;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * Injects the Provider used for load and save operations.
         * @param \Brickoo\Config\Provider\Interfaces\ProviderInterface $Provider
         * @return void
         */
        public function __construct(\Brickoo\Config\Provider\Interfaces\ProviderInterface $Provider)
        {
            parent::__construct();
            $this->_Provider = $Provider;
        }

        /**
         * Loads the configuration through the Provider.
         * @throws Exceptions\ProviderNotAvailableException if no Provider has been set
         * @return \Brickoo\Config\Configuration
         */
        public function load()
        {
            $this->fromArray($this->Provider()->load());

            return $this;
        }

        /**
         * Saves the current configuration through the Provider.
         * @throws Exceptions\ProviderNotAvailableException if no Provider has been set
         * @return \Brickoo\Config\Configuration
         */
        public function save()
        {
            $this->Provider()->save($this->toArray());

            return $this;
        }

        /**
         * Converts a configuration section settings to constants.
         * @param string $section the configuration 1st level section
         * @throws \UnexpectedValueException if the section does not exist
         * @return \Brickoo\Config\Configuration
         */
        public function convertSectionToConstants($section)
        {
            TypeValidator::IsString($section);

            if (($settings = $this->get($section)) === null) {
                throw new \UnexpectedValueException(sprintf("The section `%s` does not exist.", $section));
            }

            foreach($settings as $key => $value) {
                if (! is_scalar($value)) {
                    throw new \UnexpectedValueException(sprintf("The value of `%s` is not scalar.", $key));
                }

                $constKey = strtoupper($section ."_". $key);

                if (! defined($constKey)) {
                    define($constKey, $value);
                }
            }

            return $this;
        }

    }