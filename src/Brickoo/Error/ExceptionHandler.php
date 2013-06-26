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

    namespace Brickoo\Error;

    use Brickoo\Error\Event\ExceptionEvent,
        Brickoo\Validator\Argument;

    /**
     * ExceptionHandler
     *
     * Handles user defined or system exception.
     * Exceptions can be logged through the log event which is triggered if exceptions occured.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ExceptionHandler {

        /** @var boolean */
        private $isRegistered;

        /** @var \Brickoo\Event\Interfaces\Manager */
        private $EventManager;

        /**
         * Class constructor.
         * @param \Brickoo\Event\Interfaces\Manager $EventManager
         * @return void
         */
        public function __construct(\Brickoo\Event\Interfaces\Manager $EventManager) {
            $this->EventManager = $EventManager;
            $this->isRegistered = false;
        }

        /**
         * Checks if the instance is registered as an exception handler.
         * @return boolean check result
         */
        public function isRegistered() {
            return ($this->isRegistered === true);
        }

        /**
         * Registers the instance as exception handler.
         * @throws DuplicateHandlerRegistrationException if the instance is already registred
         * @return \Brickoo\Error\ExceptionHandler
         */
        public function register() {
            if ($this->isRegistered()) {
                throw new Exceptions\DuplicateHandlerRegistration("ExceptionHandler");
            }

            set_exception_handler(array($this, "handleException"));
            $this->isRegistered = true;

            return $this;
        }

        /**
         * Unregisters the instance as exception handler by restoring previous exception handler.
         * @throws HandlerNotRegisteredException if the instance is not registred as handler
         * @return \Brickoo\Error\ExceptionHandler
         */
        public function unregister() {
            if (! $this->isRegistered()) {
                throw new Exceptions\HandlerNotRegistered("ExceptionHandler");
            }

            restore_exception_handler();
            $this->isRegistered = false;

            return $this;
        }

        /**
         * Unregister the ExceptionHandler on destruction.
         * @return void
         */
        public function __destruct() {
            if ($this->isRegistered()) {
                $this->unregister();
            }
        }

        /**
         * Handles the exception throwed by the user or system.
         * Notifies a log event containing the exception message.
         * @param \Exception $Exception the exception throwed
         * @return string the exception message
         */
        public function handleException(\Exception $Exception) {
            $this->EventManager->notify(new ExceptionEvent($Exception));
        }

    }