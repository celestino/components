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

    use Brickoo\Library\Error\Exceptions\ErrorHandlerException;

    use Brickoo\Library\Core;
    use Brickoo\Library\Log;
    use Brickoo\Library\Error\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * ExceptionHandler
     *
     * Handles user defined or system exception.
     * Exceptions can be logged using an instance implementing the LogHandlerInterface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ExceptionHandler extends AbstractHandler
    {

        /**
         * Hold the setting for displaying exceptions.
         * @var boolean
         */
        public $displayExceptions;

        /**
         * Holds the status of instance registration as exception handler.
         * @var boolean
         */
        protected $isRegistered;

        /**
         * Checks if the instance is registered as an exception handler.
         * @return boolean check result
         */
        public function isRegistered()
        {
            return ($this->isRegistered === true);
        }

        /**
         * Registers the instance as exception handler.
         * @throws DuplicateHandlerRegistrationException if the instance is already registred
         * @return object reference
         */
        public function register()
        {
            if ($this->isRegistered())
            {
                throw new Exceptions\DuplicateHandlerRegistrationException('ExceptionHandler');
            }

            set_exception_handler(array($this, 'handleException'));
            $this->isRegistered = true;

            return $this;
        }

        /**
         * Unregisters the instance as exception handler by restoring previous exception handler.
         * @throws HandlerNotRegisteredException if the instance is not registred as handler
         * @return object reference
         */
        public function unregister()
        {
            if (! $this->isRegistered())
            {
                throw new Exceptions\HandlerNotRegisteredException('ExceptionHandler');
            }

            restore_exception_handler();
            $this->isRegistered = false;

            return $this;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->reset();
        }

        /**
         * Resets the object properties.
         * @return object reference
         */
        public function reset()
        {
            if ($this->isRegistered())
            {
                $this->unregister();
            }

            $this->displayExceptions    = false;
            $this->isRegistered         = false;

            return $this;
        }

        /**
         * Returns the exception message created by the exception content.
         * @param Exception $Exception the Exception throwed
         * @return string the exception message
         */
        protected function getExceptionMessage(\Exception $Exception)
        {
            $message     = '[' . $Exception->getCode() . ']: ';
            $message    .= $Exception->getMessage();

            if (! $Exception instanceof ErrorHandlerException)
            {
                $message    .= ' throwed in ' . $Exception->getFile();
                $message    .= ' on line ' . $Exception->getLine();
            }

            return $message;
        }

        /**
         * Handles the exception throwed by the user or system.
         * Uses the LogHandler if assigned or displays the exception message.
         * @param integer $errorCode the error code number
         * @return boolean if an LogHandler is used otherwise void
         */
        public function handleException(\Exception $Exception)
        {
            $message = $this->getExceptionMessage($Exception);

            if ($this->hasLogger())
            {
                return $this->getLogger()->log($message, Log\Logger::SEVERITY_ERROR);
            }

            if ($this->displayExceptions !== false)
            {
                $this->unregister();

                throw $Exception;
            }
        }

    }

?>