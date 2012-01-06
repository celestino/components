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

    use Brickoo\Library\Storage\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Locker
     *
     * Holds locked identifers.
     * This class can be used to have keep an lock status on specific identifiers.
     * Contains one abstract method (public boolean Locker::isIdentifierAvailable($identifier)).
     * This abstract method is to allow only identifiers which are available on the main class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    abstract class Locker implements \Countable
    {

        protected $locked;

        /**
         * Clearn up the identifiers which are not available or locked.
         * @param array $identifiers the identifiers to clean up.
         * @return array the cleaned identifiers
         */
        protected function cleanToLockIdentifiers(array $identifiers)
        {
            foreach($identifiers as $index => $singleIdentifier)
            {
                TypeValidator::IsStringOrInteger($singleIdentifier);

                if
                (
                    (! $this->isIdentifierAvailable($singleIdentifier)) ||
                    $this->isLocked($singleIdentifier)
                )
                {
                    unset ($identifiers[$index]);
                    continue;
                }

                $identifiers[$index] = $singleIdentifier;
            }

            return $identifiers;
        }

        /**
         * Locks the identifer(s).
         * Fails if a single identifier or all identifiers are locked.
         * If a array is given and just a few identifiers
         * are locked or not registered the method does not fail.
         * Extended by the Registry class, override and unregister methods of the
         * Registry class are disabled for this identifier(s)
         * @param string|integer|array $identifiers the identifiers to lock
         * @throws LockFailedException if all the identifiers can not be locked
         * @return object reference
         */
        public function lock($identifiers)
        {
            if (! is_array($identifiers))
            {
                $identifiers = array($identifiers);
            }

            $identifiers = $this->cleanToLockIdentifiers($identifiers);

            if (empty($identifiers))
            {
                throw new Exceptions\LockFailedException();
            }

            $this->locked = array_merge($this->locked, $identifiers);

            // TODO: Return an lock key !
            return $this;
        }

        /**
         * Clearn up the identifiers which are not locked.
         * @param array $identifiers the identifiers to clean up.
         * @return array the cleaned identifiers
         */
        protected function cleanToUnlockIdentifiers(array $identifiers)
        {
            foreach($identifiers as $index => $singleIdentifier)
            {
                TypeValidator::IsStringOrInteger($singleIdentifier);

                if(! $this->isLocked($singleIdentifier))
                {
                    unset ($identifiers[$index]);
                    continue;
                }

                $identifiers[$index] = $singleIdentifier;
            }

            return $identifiers;
        }

        /**
         * Unlocks the currently locked identifer(s).
         * Fails if the single identifier or all identifers of an array are not locked.
         * If a array is given and just a few identifiers
         * are not locked the method does not fail.
         * @param string|integer|array $identifiers the identifier(s) which should be unlocked
         * @throws UnlockFailedException if all the identifiers can not be unlocked
         * @return object reference
         */
        public function unlock($identifiers)
        {
            if (! is_array($identifiers))
            {
                $identifiers = array($identifiers);
            }

            $identifiers = $this->cleanToUnlockIdentifiers($identifiers);

            if (empty($identifiers))
            {
                throw new Exceptions\UnlockFailedException();
            }

            $this->locked = array_diff($this->locked, $identifiers);

            return $this;
        }

        /**
         * Checks if the identifier is currently locked.
         * @param string|integer $identifier the identifier to check
         * @return boolean check result
         */
        public function isLocked($identifier)
        {
            TypeValidator::IsStringOrInteger($identifier);

            return in_array($identifier, $this->locked);
        }

        /**
         * Returns the amount of locked identifiers.
         * This method is for the case of overriden Locker::count()
         * @return integer the number of locked identifiers
         */
        public function getAmountOfLockedIdentifiers()
        {
            return count($this->locked);
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->resetLocker();
        }

        /**
         * Clears the class properties.
         * @return object reference
         */
        public function resetLocker()
        {
            $this->locked = array();

            return $this;
        }

        /**
         * Countable interface function.
         * Returns the number of locked identifiers.
         * @see Countable::count()
         * @return integer the number of locked identifiers
         */
        public function count()
        {
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

?>