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
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Registry extends Locker {

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
    public function __construct(array $registrations = [], $readOnly = false) {
        parent::__construct();
        $this->registrations = $registrations;
        $this->readOnly = $readOnly;
    }

    /**
     * Returns all assigned registrations.
     * @return array the assigned registrations
     */
    public function getAll() {
        return $this->registrations;
    }

    /**
     * Adds a list of registrations to the registry.
     * @param array $registrations the registrations to add
     * @throws \InvalidArgumentException if passed registrations is empty
     * @return \Brickoo\Memory\Registry
     */
    public function add(array $registrations) {
        foreach($registrations as $identifier => $value) {
            $this->register($identifier, $value);
        }
        return $this;
    }

    /**
     * Returns the registered value from the given identifier.
     * @param string|integer $identifier the identifier so retrieve the value from
     * @throws \InvalidArgumentException if the identifier is not valid
     * @throws \Brickoo\Memory\Exception\IdentifierNotRegisteredException if the identifier is not registered
     * @return mixed the value of the registered identifier
     */
    public function get($identifier) {
        Argument::IsStringOrInteger($identifier);

        if (! $this->isRegistered($identifier)) {
            throw new Exception\IdentifierNotRegisteredException($identifier);
        }

        return $this->registrations[$identifier];
    }

    /**
     * Register an identifer-value pair.
     * Take care of registering objects who are assigned somewhere else
     * as an reference the changes applys to the registerd objects as well.
     * @param string|integer $identifier the identifier to register
     * @param mixed $value the identifier value to reguister with
     * @throws \Brickoo\Memory\Exception\DuplicateRegistrationException the identifier is already registered
     * @throws \Brickoo\Memory\Exception\ReadonlyModeException if the mode is currently read only
     * @return \Brickoo\Memory\Registry
     */
    public function register($identifier, $value) {
        Argument::IsStringOrInteger($identifier);

        if ($this->isReadOnly()) {
            throw new Exception\ReadonlyModeException();
        }

        if ($this->isRegistered($identifier)) {
            throw new Exception\DuplicateRegistrationException($identifier);
        }

        $this->registrations[$identifier] = $value;
        return $this;
    }

    /**
     * Overrides an existing identifier with the new value (!).
     * If the identifer ist not registered it will be registered.
     * @param string|integer $identifier the identifier to register
     * @param mixed $value the identifier value to register
     * @throws \Brickoo\Memory\Exception\ReadonlyModeException if the mode is currently read only
     * @throws \Brickoo\Memory\Exception\IdentifierLockedException if the identifier is locked
     * @return \Brickoo\Memory\Registry
     */
    public function override($identifier, $value) {
        Argument::IsStringOrInteger($identifier);

        if ($this->isReadOnly()) {
            throw new Exception\ReadonlyModeException();
        }

        if ($this->isLocked($identifier)) {
            throw new Exception\IdentifierLockedException($identifier);
        }

        $this->registrations[$identifier] = $value;
        return $this;
    }

    /**
     * Unregister the indentifier and his value.
     * @param string|integer $identifier the identifier to unregister
     * @throws \Brickoo\Memory\Exception\ReadonlyModeException if the mode is currently read only
     * @throws \Brickoo\Memory\Exception\IdentifierLockedException if the identifier is locked
     * @throws \Brickoo\Memory\Exception\IdentifierNotRegisteredException if the identifier is not registered
     * @return \Brickoo\Memory\Registry
     */
    public function unregister($identifier) {
        Argument::IsStringOrInteger($identifier);

        if ($this->isReadOnly()) {
            throw new Exception\ReadonlyModeException();
        }

        if ($this->isLocked($identifier)) {
            throw new Exception\IdentifierLockedException($identifier);
        }

        if (! $this->isRegistered($identifier)) {
            throw new Exception\IdentifierNotRegisteredException($identifier);
        }

        unset ($this->registrations[$identifier]);
        return $this;
    }

    /**
     * Check if the identifier is registered.
     * @param string|integer $identifier the identifier to check
     * @return boolean check result
     */
    public function isRegistered($identifier) {
        Argument::IsStringOrInteger($identifier);

        return array_key_exists($identifier, $this->registrations);
    }

    /**
     * Sets the read only mode for all registrations.
     * True to allow read only, write operations will be not allowed.
     * False for enable read and write operations, locked identifiers will still being locked .
     * @param boolean $mode the mode to set
     * @return \Brickoo\Memory\Registry
     */
    public function setReadOnly($mode = true) {
        Argument::IsBoolean($mode);

        $this->readOnly = $mode;
        return $this;
    }

    /**
     * Check if the mode is currently set to read only.
     * @return boolean read only mode
     */
    public function isReadOnly() {
        return $this->readOnly;
    }

    /** {@inheritDoc} */
    public function isIdentifierAvailable($identifier) {
        return $this->isRegistered($identifier);
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