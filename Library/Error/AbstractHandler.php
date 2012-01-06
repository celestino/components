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

    namespace Brickoo\Library\Error;

    use Brickoo\Library\Log;
    use Brickoo\Library\Core;

    /**
     * AbstractHandler
     *
     * AbstractHandler includes the Logger dependecy injection.
     * Declares methods needed to be implemented.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    abstract class AbstractHandler
    {

        /**
        * Checks if the instance is registered as an error/exception handler.
        * @return boolean check result
        */
        abstract public function isRegistered();

        /**
         * Registers the instance as error/exception handler.
         * @throws DuplicateHandlerRegistrationException if the instance is already registred
         * @return object reference
         */
        abstract public function register();

        /**
         * Unregisters the instance as error/exception handler by restoring previous error/exception handler.
         * @throws HandlerNotRegisteredException if the instance is not registred as handler
         * @return object reference
         */
        abstract public function unregister();

        /**
         * Holds an instance of an Logger.
         * @var Brickoo\Library\Log\Interfaces\LoggerInterface
         */
        protected $Logger;

        /**
         * Returns the Logger dependency injected
         * @throws Core\Exceptions\DependencyNotAvailableException if the dependency is not available
         * @return object Logger implementing the Log\Interfaces\LoggerInterface
         */
        public function getLogger()
        {
            if (! $this->hasLogger())
            {
                throw new Core\Exceptions\DependencyNotAvailableException('LoggerInterface');
            }

            return $this->Logger;
        }

        /**
         * Injects an Logger instance implementing the LoggerInterface.
         * @param LoggerInterface $Logger the Logger dependency
         * @throws DependencyOverwriteException if trying to override the current dependency
         * @return object reference
         */
        public function injectLogger(\Brickoo\Library\Log\Interfaces\LoggerInterface $Logger)
        {
            if ($this->hasLogger())
            {
                throw new Core\Exceptions\DependencyOverwriteException('LoggerInterface');
            }

            $this->Logger = $Logger;

            return $this;
        }

        /**
         * Removes the assigned log handler.
         * @throws DependencyNotAvailableException if trying to remove an not assigned dependency
         * @return object reference
         */
        public function removeLogger()
        {
            if (! $this->hasLogger())
            {
                throw new Core\Exceptions\DependencyNotAvailableException('LoggerInterface');
            }

            $this->Logger = null;

            return $this;
        }

        /**
         * Checks if the error handler instance has an log handler assigned.
         * @return boolean check result
         */
        public function hasLogger()
        {
            return ($this->Logger instanceof Log\Interfaces\LoggerInterface);
        }

    }

?>