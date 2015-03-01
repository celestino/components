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

use Brickoo\Component\Common\Exception\LockFailedException;
use Brickoo\Component\Common\Exception\UnlockFailedException;
use Brickoo\Component\Common\Assert;

/**
 * Locker
 *
 * This class can be used to have keep an lock status on specific identifiers.
 * Contains one abstract method (public boolean isIdentifierAvailable($identifier)).
 * The abstract method must be implemented to assure the existence of identifiers.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

abstract class Locker implements \Countable {

    /**
     * Holds the locked identifier associated to the keys.
     * @var array
     */
    protected $locked;

    public function __construct() {
        $this->locked = [];
    }

    /**
     * Locks the identifier and returns an unlock key.
     * Extended by the Registry class, override and unregister methods of the
     * Registry class are disabled for this identifier(s)
     * @param string $identifier the identifier to lock
     * @throws \Brickoo\Component\Common\Exception\LockFailedException
     * @return string the unlock key
     */
    public function lock($identifier) {
        Assert::isString($identifier);

        if ((! $this->isIdentifierAvailable($identifier)) || $this->isLocked($identifier)) {
            throw new LockFailedException($identifier);
        }

        $this->locked[$identifier] = ($unlockKey = uniqid($identifier));
        return $unlockKey;
    }

    /**
     * Unlocks the locked identifier matching the lock key.
     * @param string $identifier the identifier which should be unlocked
     * @param string $unlockKey the key to unlock the identifier
     * @throws \Brickoo\Component\Common\Exception\UnlockFailedException
     * @return \Brickoo\Component\Common\Locker
     */
    public function unlock($identifier, $unlockKey) {
        Assert::isString($identifier);
        Assert::isString($unlockKey);

        if(! $this->isLocked($identifier) || ($this->locked[$identifier] !== $unlockKey)) {
            throw new UnlockFailedException($identifier);
        }

        unset($this->locked[$identifier]);
        return $this;
    }

    /**
     * Checks if the identifier is currently locked.
     * @param string $identifier the identifier to check
     * @return boolean check result
     */
    public function isLocked($identifier) {
        Assert::isString($identifier);
        return array_key_exists($identifier, $this->locked);
    }

    /**
     * Countable interface function.
     * Returns the number of locked identifiers.
     * @see Countable::count()
     * @return integer the number of locked identifiers
     */
    public function count() {
        return count($this->locked);
    }

    /**
     * Abstract method needed to check if the identifier is available.
     * @param string $identifier the identifier to check
     * @return boolean check result
     */
    abstract public function isIdentifierAvailable($identifier);

}
