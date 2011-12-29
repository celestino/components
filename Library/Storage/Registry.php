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

    namespace Brickoo\Library\Storage;

    use Brickoo\Library\Storage\Interfaces;
    use Brickoo\Library\Storage\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Registry
     *
     * Registrations are stored as key value pairs.
     * Provides getter and setter for accessing the registrations.
     * Provides lock functionality for each identifer and an read only mode for all identifiers.
     * This class can be used for example as an class properties container.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id$
     */

    class Registry extends Locker implements Interfaces\RegistryInterface, \Countable
    {

        /**
         * Holds the registered identifier-value pairs.
         * @var array
         */
        protected $registrations;

        /**
         * Returns all assigned registrations.
         * @return array the assigned registrations
         */
        public function getRegistrations()
        {
            return $this->registrations;
        }

        /**
         * Adds the list of registrations to the registry.
         * @param array $registrations the registrations to add
         * @throws InvalidArgumentException if passed registrations is empty
         * @return object reference
         */
        public function addRegistrations(array $registrations)
        {
            TypeValidator::Validate('isArray', array($registrations));

            foreach($registrations as $identifier => $value)
            {
                $this->register($identifier, $value);
            }

            return $this;
        }

        /**
         * Returns the registered value from the given identifier.
         * @param string|integer $identifier the identifier so retrieve the value from
         * @throws InvalidArgumentException if the identifier is not valid
         * @throws IdentifierNotRegisteredException if the identifier is not registered
         * @return mixed the value of the registered identifier
         */
        public function getRegistered($identifier)
        {
            TypeValidator::Validate('isStringOrInteger', array($identifier));

            if (! $this->isRegistered($identifier))
            {
                throw new Exceptions\IdentifierNotRegisteredException($identifier);
            }

            return $this->registrations[$identifier];
        }

        /**
         * Register an identifer-value pair.
         * Take care of registering objects who are assigned somewhere else
         * as an reference the changes applys to the registerd objects as well.
         * @param string|integer $identifier the identifier to register
         * @param mixed $value the identifier value to reguister with
         * @throws DuplicateRegistrationException the identifier is already registered
         * @throws ReadonlyModeException if the mode is currently read only
         * @return object reference
         */
        public function register($identifier, $value)
        {
            TypeValidator::Validate('isStringOrInteger', array($identifier));

            if ($this->isReadOnly())
            {
                throw new Exceptions\ReadonlyModeException();
            }

            if ($this->isRegistered($identifier))
            {
                throw new Exceptions\DuplicateRegistrationException($identifier);
            }

            $this->registrations[$identifier] = $value;

            return $this;
        }

        /**
         * Overrides an existing identifier with given value (!).
         * If the identifer ist not registered it will be registered.
         * @param string|integer $identifier the identifier to register
         * @param mixed $value the identifier value to register
         * @throws ReadonlyModeException if the mode is currently read only
         * @throws IdentifierLockedException if the identifier is locked
         * @return object reference
         */
        public function override($identifier, $value)
        {
            TypeValidator::Validate('isStringOrInteger', array($identifier));

            if ($this->isReadOnly())
            {
                throw new Exceptions\ReadonlyModeException();
            }

            if ($this->isLocked($identifier))
            {
                throw new Exceptions\IdentifierLockedException($identifier);
            }

            $this->registrations[$identifier] = $value;

            return $this;
        }

        /**
         * Unregister the indentifier and his value.
         * @param string|integer $identifier the identifier to unregister
         * @throws ReadonlyModeException if the mode is currently read only
         * @throws IdentifierLockedException if the identifier is locked
         * @throws IdentifierNotRegisteredException if the identifier is not registered
         * @return object reference
         */
        public function unregister($identifier)
        {
            TypeValidator::Validate('isStringOrInteger', array($identifier));

            if ($this->isReadOnly())
            {
                throw new Exceptions\ReadonlyModeException();
            }

            if ($this->isLocked($identifier))
            {
                throw new Exceptions\IdentifierLockedException($identifier);
            }

            if (! $this->isRegistered($identifier))
            {
                throw new Exceptions\IdentifierNotRegisteredException($identifier);
            }

            unset ($this->registrations[$identifier]);

            return $this;
        }

        /**
         * Abstract method used by Locker class.
         * Alias for the check if an identifier is registered
         * @param string|integer $identifier the identifier to check
         * @return boolean check result
         * @see Brickoo\Library\Storage\Locker::isIdentifierAvailable()
         */
        public function isIdentifierAvailable($identifier)
        {
            return $this->isRegistered($identifier);
        }

        /**
         * Check if the identifier is registered.
         * @param string|integer $identifier the identifier to check
         * @return boolean check result
         */
        public function isRegistered($identifier)
        {
            TypeValidator::Validate('isStringOrInteger', array($identifier));

            return array_key_exists($identifier, $this->registrations);
        }

        /**
         * Holds the status of the current read / write mode.
         * @var boolean
         */
        protected $readOnly;

        /**
         * Sets the read only mode for all registrations.
         * True to allow read only, all write are not allowed.
         * False for read and all write operations,
         * locked identifiers will still being locked .
         * @param boolean $mode the mode to set
         * @return object reference
         */
        public function setReadOnly($mode = true)
        {
            TypeValidator::Validate('isBoolean', array($mode));

            $this->readOnly = $mode;

            return $this;
        }

        /**
         * Check if the mode is currently set to read only.
         * @return boolean read only mode
         */
        public function isReadOnly()
        {
            return $this->readOnly;
        }

        /**
         * Returns the value of the identifier from the registrations container.
         * @param string|integer $identifier the identifer to retrieve the value from
         * @return mixed the corresponding identifer value
         */
        public function __get($identifier)
        {
            return $this->getRegistered($identifier);
        }

        /**
         * Adds the identifer and his value to the registrations container.
         * @param string|integer $identifier the identifier to register
         * @param mixed $value the value of the identifier
         * @return object reference
         */
        public function __set($identifier, $value)
        {
            $this->register($identifier, $value);
            return $this;
        }

        /**
         * Registry constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->clearRegistry();
        }

        /**
         * Clears the class propeties.
         * @return object reference
         */
        public function clearRegistry()
        {
            $this->clearLocker();
            $this->registrations    = array();
            $this->readOnly         = false;

            return $this;
        }

        /**
         * Countable interface function.
         * Returns the number of registrations.
         * @see Countable::count()
         * @return integer the number of registrations
         */
        public function count()
        {
            return count($this->registrations);
        }

        /**
         * Returns the number of locked identifiers.
         * @return integer the number of locked identifiers
         */
        public function countLocked()
        {
            return count($this->locked);
        }

    }

?>