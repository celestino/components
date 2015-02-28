<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Component\Common;

use Brickoo\Component\Common\Exception\DuplicateRegistrationException;
use Brickoo\Component\Common\Exception\IdentifierLockedException;
use Brickoo\Component\Common\Exception\IdentifierNotRegisteredException;
use Brickoo\Component\Common\Exception\ReadonlyModeException;
use Brickoo\Component\Validation\Argument;

/**
 * Registry
 *
 * Registrations are stored as key value pairs.
 * Provides getter and setter for accessing the registrations.
 * Provides lock functionality for each identifier and an read only mode for all identifiers.
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
     * @param boolean $readOnly initialize mode for the registry
     */
    public function __construct(array $registrations = [], $readOnly = false) {
        parent::__construct();
        $this->registrations = $registrations;
        $this->readOnly = (! empty($registrations)) && (boolean)$readOnly;
    }

    /**
     * Return all assigned registrations as an array.
     * @return array
     */
    public function toArray() {
        return $this->registrations;
    }

    /**
     * Add a registration to the registry.
     * @param mixed $registrations the registrations to add
     * @throws \InvalidArgumentException if passed registrations is empty
     * @return \Brickoo\Component\Common\Registry
     */
    public function add($registrations) {
        if ((! is_array($registrations)) && (! $registrations instanceof \Traversable)) {
            $registrations = [$registrations];
        }

        foreach($registrations as $identifier => $value) {
            $this->register($identifier, $value);
        }
        return $this;
    }

    /**
     * Return the registered value from the given identifier.
     * @param string $identifier the identifier so retrieve the value from
     * @throws \InvalidArgumentException if the identifier is not valid
     * @throws \Brickoo\Component\Common\Exception\IdentifierNotRegisteredException
     * @return mixed
     */
    public function get($identifier) {
        Argument::isStringOrInteger($identifier);

        if (! $this->isRegistered($identifier)) {
            throw new IdentifierNotRegisteredException($identifier);
        }

        return $this->registrations[$identifier];
    }

    /**
     * Register an identifier-value pair.
     * Take care of registering objects who are assigned somewhere else
     * as an reference the changes applies to the registered objects as well.
     * @param string $identifier the identifier to register
     * @param mixed $value the identifier value to register with
     * @throws \Brickoo\Component\Common\Exception\DuplicateRegistrationException
     * @throws \Brickoo\Component\Common\Exception\ReadonlyModeException
     * @return \Brickoo\Component\Common\Registry
     */
    public function register($identifier, $value) {
        Argument::isStringOrInteger($identifier);

        if ($this->isRegistered($identifier)) {
            throw new DuplicateRegistrationException($identifier);
        }

        $this->set($identifier, $value);
        return $this;
    }

    /**
     * Overrides an existing identifier with the new value (!).
     * If the identifier ist not registered it will be registered.
     * @param string $identifier the identifier to register
     * @param mixed $value the identifier value to register
     * @throws \Brickoo\Component\Common\Exception\ReadonlyModeException
     * @throws \Brickoo\Component\Common\Exception\IdentifierLockedException
     * @return \Brickoo\Component\Common\Registry
     */
    public function override($identifier, $value) {
        Argument::isStringOrInteger($identifier);

        if ($this->isLocked($identifier)) {
            throw new IdentifierLockedException($identifier);
        }

        $this->set($identifier, $value);
        return $this;
    }

    /**
     * Unregister the identifier and his value.
     * @param string $identifier the identifier to unregister
     * @throws \Brickoo\Component\Common\Exception\ReadonlyModeException
     * @throws \Brickoo\Component\Common\Exception\IdentifierLockedException
     * @throws \Brickoo\Component\Common\Exception\IdentifierNotRegisteredException
     * @return \Brickoo\Component\Common\Registry
     */
    public function unregister($identifier) {
        Argument::isStringOrInteger($identifier);

        if ($this->isReadOnly()) {
            throw new ReadonlyModeException();
        }

        if ($this->isLocked($identifier)) {
            throw new IdentifierLockedException($identifier);
        }

        if (! $this->isRegistered($identifier)) {
            throw new IdentifierNotRegisteredException($identifier);
        }

        unset ($this->registrations[$identifier]);
        return $this;
    }

    /**
     * Check if the identifier is registered.
     * @param string $identifier the identifier to check
     * @return boolean
     */
    public function isRegistered($identifier) {
        Argument::isStringOrInteger($identifier);
        return array_key_exists($identifier, $this->registrations);
    }

    /**
     * Sets the read only mode for all registrations.
     * True to allow read only, write operations will be not allowed.
     * False for enable read and write operations, locked identifiers will still being locked .
     * @param boolean $mode the mode to set
     * @return \Brickoo\Component\Common\Registry
     */
    public function setReadOnly($mode = true) {
        Argument::isBoolean($mode);

        $this->readOnly = $mode;
        return $this;
    }

    /**
     * Check if the mode is currently set to read only.
     * @return boolean
     */
    public function isReadOnly() {
        return $this->readOnly;
    }

    /**
     * Countable interface implementation.
     * Returns the number of registrations.
     * @see Countable::count()
     * @return integer
     */
    public function count() {
        return count($this->registrations);
    }

    /**
     * Returns the number of locked identifiers.
     * @return integer
     */
    public function countLocked() {
        return count($this->locked);
    }

    /** {@inheritDoc} */
    public function isIdentifierAvailable($identifier) {
        return $this->isRegistered($identifier);
    }

    /**
     * Set the registered identifier and value.
     * @param string $identifier
     * @param mixed $value
     * @throws Exception\DuplicateRegistrationException
     * @throws Exception\ReadonlyModeException
     * @return \Brickoo\Component\Common\Registry
     */
    private function set($identifier, $value) {
        if ($this->isReadOnly()) {
            throw new ReadonlyModeException();
        }

        $this->registrations[$identifier] = $value;
        return $this;
    }

}
