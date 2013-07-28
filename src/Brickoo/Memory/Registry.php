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

    namespace Brickoo\Memory;

    use Brickoo\Validator\Argument;

    /**
     * Registry
     *
     * Registrations are stored as key value pairs.
     * Provides getter and setter for accessing the registrations.
     * Provides lock functionality for each identifer and an read only mode for all identifiers.
     * This class can be used for example as an class properties container.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Registry extends Locker implements Interfaces\Registry {

        /** @var array */
        protected $registrations;

        /** @var boolean */
        protected $readOnly;

        /**
         * Class constructor.
         * @param array $registrations the registrations to add
         * @param string $readOnly initialize mode for the registry
         * @return void
         */
        public function __construct(array $registrations = array(), $readOnly = false) {
            parent::__construct();
            $this->registrations = $registrations;
            $this->readOnly = $readOnly;
        }

        /** {@inheritDoc} */
        public function getAll() {
            return $this->registrations;
        }

        /** {@inheritDoc} */
        public function add(array $registrations) {
            foreach($registrations as $identifier => $value) {
                $this->register($identifier, $value);
            }

            return $this;
        }

        /** {@inheritDoc} */
        public function get($identifier) {
            Argument::IsStringOrInteger($identifier);

            if (! $this->isRegistered($identifier)) {
                throw new Exceptions\IdentifierNotRegisteredException($identifier);
            }

            return $this->registrations[$identifier];
        }

        /** {@inheritDoc} */
        public function register($identifier, $value) {
            Argument::IsStringOrInteger($identifier);

            if ($this->isReadOnly()) {
                throw new Exceptions\ReadonlyModeException();
            }

            if ($this->isRegistered($identifier)) {
                throw new Exceptions\DuplicateRegistrationException($identifier);
            }

            $this->registrations[$identifier] = $value;

            return $this;
        }

        /** {@inheritDoc} */
        public function override($identifier, $value) {
            Argument::IsStringOrInteger($identifier);

            if ($this->isReadOnly()) {
                throw new Exceptions\ReadonlyModeException();
            }

            if ($this->isLocked($identifier)) {
                throw new Exceptions\IdentifierLockedException($identifier);
            }

            $this->registrations[$identifier] = $value;

            return $this;
        }

        /** {@inheritDoc} */
        public function unregister($identifier) {
            Argument::IsStringOrInteger($identifier);

            if ($this->isReadOnly()) {
                throw new Exceptions\ReadonlyModeException();
            }

            if ($this->isLocked($identifier)) {
                throw new Exceptions\IdentifierLockedException($identifier);
            }

            if (! $this->isRegistered($identifier)) {
                throw new Exceptions\IdentifierNotRegisteredException($identifier);
            }

            unset ($this->registrations[$identifier]);

            return $this;
        }

        /** {@inheritDoc} */
        public function isIdentifierAvailable($identifier) {
            return $this->isRegistered($identifier);
        }

        /** {@inheritDoc} */
        public function isRegistered($identifier) {
            Argument::IsStringOrInteger($identifier);

            return array_key_exists($identifier, $this->registrations);
        }

        /** {@inheritDoc} */
        public function setReadOnly($mode = true) {
            Argument::IsBoolean($mode);

            $this->readOnly = $mode;
            return $this;
        }

        /** {@inheritDoc} */
        public function isReadOnly() {
            return $this->readOnly;
        }

        /**
         * Countable interface implementation.
         * Returns the number of registrations.
         * @see Countable::count()
         * @return integer the number of registrations
         */
        public function count() {
            return count($this->registrations);
        }

        /**
         * Returns the number of locked identifiers.
         * @return integer the number of locked identifiers
         */
        public function countLocked() {
            return count($this->locked);
        }

        /**
         * Returns the value of the identifier from the registrations container.
         * @param string|integer $identifier the identifer to retrieve the value from
         * @return mixed the corresponding identifer value
         */
        public function __get($identifier) {
            return $this->get($identifier);
        }

        /**
         * Adds the identifer and his value to the registrations container.
         * @param string|integer $identifier the identifier to register
         * @param mixed $value the value of the identifier
         * @return void
         */
        public function __set($identifier, $value) {
            $this->register($identifier, $value);
        }

        /**
         * Checks if the identifier is registered.
         * @param string|integer $identifier the indentifier to check
         * @return boolean check result
         */
        public function __isset($identifier) {
            return $this->isRegistered($identifier);
        }

        /**
         * Unsets the identifier from the registrations.
         * @param string|integer $identifier the identifier to unregister
         * @return void
         */
        public function __unset($identifier) {
            $this->unregister($identifier);
        }

    }