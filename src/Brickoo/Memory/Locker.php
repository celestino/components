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

    namespace Brickoo\Memory;

    use Brickoo\Validator\TypeValidator;

    /**
     * Locker
     *
     * This class can be used to have keep an lock status on specific identifiers.
     * Contains one abstract method (public boolean Locker::isIdentifierAvailable($identifier)).
     * The abstract method is implemented to allow only identifiers which are available on the main class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    abstract class Locker implements \Countable {

        /**
         * Holds the locked identifier associated to the keys.
         * @var array
         */
        protected $locked;

        /**
         * Locks the identifer and returns an unlock key.
         * Extended by the Registry class, override and unregister methods of the
         * Registry class are disabled for this identifier(s)
         * @param string|integer|array $identifiers the identifiers to lock
         * @throws LockFailedException if all the identifiers can not be locked
         * @return string the unlock key
         */
        public function lock($identifier) {
            TypeValidator::IsStringOrInteger($identifier);

            if ((! $this->isIdentifierAvailable($identifier)) || $this->isLocked($identifier)) {
                throw new Exceptions\LockFailedException($identifier);
            }

            $this->locked[$identifier] = ($unlockKey = uniqid($identifier));

            return $unlockKey;
        }

        /**
         * Unlocks the locked identifer matching the lock key.
         * @param string|integer $identifier the identifier which should be unlocked
         * @param string $unlockKey the key to unlock the identifier
         * @throws UnlockFailedException if all the identifiers can not be unlocked
         * @return \Brickoo\Memory\Locker
         */
        public function unlock($identifier, $unlockKey) {
            TypeValidator::IsStringOrInteger($identifier);
            TypeValidator::IsStringAndNotEmpty($unlockKey);

            if(! $this->isLocked($identifier) || ($this->locked[$identifier] !== $unlockKey)) {
                throw new Exceptions\UnlockFailedException($identifier);
            }

            unset($this->locked[$identifier]);

            return $this;
        }

        /**
         * Checks if the identifier is currently locked.
         * @param string|integer $identifier the identifier to check
         * @return boolean check result
         */
        public function isLocked($identifier) {
            TypeValidator::IsStringOrInteger($identifier);

            return array_key_exists($identifier, $this->locked);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct() {
            $this->locked = array();
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
         * Abstract method needed to check if the identifier
         * is available to be locked or unlocked
         * @param string|integer $identifier the identifier to check
         * @return boolean check result
         */
        abstract public function isIdentifierAvailable($identifier);

    }