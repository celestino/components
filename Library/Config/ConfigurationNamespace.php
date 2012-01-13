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

    namespace Brickoo\Library\Config;

    use Brickoo\Library\Core;
    use Brickoo\Library\Config\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * ConfigurationNamespace
     *
     * Provides a namespace for holding configuration related data.
     * While using a namespace the namespace can not be redeclared again.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ConfigurationNamespace implements Interfaces\ConfigurationNamespaceInterface
    {

        /**
         * Holds the declared namspaces.
         * This namespaces can not be redeclared again.
         * @var array
         */
        protected static $ReservedNamespaces;

        /**
         * Returns the currently reserved namespaces.
         * @return array the reserved namespaces as values
         */
        public static function GetReservedNamespaces()
        {
            if (static::$ReservedNamespaces === null)
            {
                static::$ReservedNamespaces = array();
            }

            return static::$ReservedNamespaces;
        }

        /**
         * Adds a reserved namspace to the static content.
         * @param string $namespace the namespace to add
         * @return void
         */
        protected static function AddReservedNamespace($namespace)
        {
            TypeValidator::IsString($namespace);

            static::$ReservedNamespaces[] = $namespace;
        }

        /**
         * Checks if the namespace is already reserved.
         * @param string $namespace the namespace to check
         * return boolean check result
         */
        public static function IsNamespaceReserved($namespace)
        {
            TypeValidator::IsString($namespace);

            return in_array($namespace, static::GetReservedNamespaces());
        }

        /**
         * Holds the namespace to work with.
         * @var string
         */
        protected $namespace;

        /**
         * Returns the namespace working with.
         * @return string the namespace working with
         */
        public function getNamespace()
        {
            return $this->namespace;
        }

        /**
         * Holds the configuration added.
         * @var array
         */
        protected $configuration;

        /**
         * Checks if the configuration of the identifier is available.
         * @param string $identifier the identifier to check
         * @return boolean check result
         */
        public function hasConfiguration($identifier)
        {
            TypeValidator::IsString($identifier);

            return array_key_exists($identifier, $this->configuration);
        }

        /**
         * Sets the content to be holded by the identifier.
         * @param string $identifier the identifier to attach the content to
         * @param mixed $content the content to attach to the identifier
         * @return \Brickoo\Library\Config\ConfigurationNamespace
         */
        public function setConfiguration($identifier, $content)
        {
            TypeValidator::IsString($identifier);

            if ($this->hasConfiguration($identifier))
            {
                throw new Core\Exceptions\ValueOverwriteException($this->namespace . '::' . $identifier);
            }

            $this->configuration[$identifier] = $content;

            return $this;
        }

        /**
         * Returns the content holded by the identifier or the default content if the identifier is not available.
         * @param string $identifier the identifier to retrieve the content from
         * @param mixed $defaultValue the default content to return if the identifier is not available
         * @return mixed the identifier holded content otherwise the default content
         */
        public function getConfiguration($identifier, $defaultValue = null)
        {
            TypeValidator::IsString($identifier);

            if (! $this->hasConfiguration($identifier))
            {
                return $defaultValue;
            }

            return $this->configuration[$identifier];
        }

        /**
         * Class constructor.
         * Checks if the namespace is reserved.
         * Initializes the calss properties.
         * @param string $namespace the namespace to reserve and work with
         * @throws Exceptions\NamespaceReservedException if the namespace is already reserved
         * @return void
         */
        public function __construct($namespace)
        {
            TypeValidator::IsString($namespace);

            if (static::IsNamespaceReserved($namespace))
            {
                throw new Exceptions\NamespaceReservedException($namespace);
            }

            static::AddReservedNamespace($namespace);

            $this->namespace        = $namespace;
            $this->configuration    = array();
        }


    }

?>