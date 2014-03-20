<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Component\Error;

use Brickoo\Component\Messaging\MessageDispatcher,
    Brickoo\Component\Error\Message\ErrorMessage,
    Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException,
    Brickoo\Component\Error\Exception\ErrorOccurredException,
    Brickoo\Component\Error\Exception\HandlerNotRegisteredException,
    Brickoo\Component\Validation\Argument;

/**
 * ErrorHandler
 *
 * Handles user defined and system errors of an expected error level.
 * Errors can be automatic converted to exceptions.
 * Triggers an log message if a message dispatcher is attached.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ErrorHandler {

    /** @var boolean */
    private $convertToException;

    /** @var boolean */
    private $isRegistered;

    /** @var \Brickoo\Component\Messaging\MessageDispatcher */
    private $messageDispatcher;

    /**
     * Class constructor.
     * Initializes the error handler.
     * @param \Brickoo\Component\Messaging\MessageDispatcher $messageDispatcher
     * @param boolean $convertToException flag to convert errors to exceptions
     */
    public function __construct(MessageDispatcher $messageDispatcher, $convertToException = true) {
        Argument::IsBoolean($convertToException);
        $this->messageDispatcher = $messageDispatcher;
        $this->convertToException = $convertToException;
        $this->isRegistered = false;
    }

    /**
     * Checks if the instance is registered as an error handler.
     * @return boolean check result
     */
    public function isRegistered() {
        return ($this->isRegistered === true);
    }

    /**
     * Registers the instance as error handler.
     * @throws \Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException
     * @return \Brickoo\Component\Error\ErrorHandler
     */
    public function register() {
        if ($this->isRegistered()) {
            throw new DuplicateHandlerRegistrationException("ErrorHandler");
        }
        set_error_handler(array($this, "handleError"));
        $this->isRegistered = true;
        return $this;
    }

    /**
     * Unregisters the instance as error handler by restoring previous error handler.
     * @throws \Brickoo\Component\Error\Exception\HandlerNotRegisteredException
     * @return \Brickoo\Component\Error\ErrorHandler
     */
    public function unregister() {
        if (! $this->isRegistered()) {
            throw new HandlerNotRegisteredException("ErrorHandler");
        }
        restore_error_handler();
        $this->isRegistered = false;
        return $this;
    }

    /**
     * Unregister the ErrorHandler on object destruction.
     * @return void
     */
    public function __destruct() {
        if ($this->isRegistered()) {
            $this->unregister();
        }
    }

    /**
     * Handles the error reported by the user or system.
     * Converts the error to an exception if configured.
     * @param integer $errorCode the error code number
     * @param string $errorMessage the error message
     * @param string $errorFile the error file name
     * @param integer $errorLine the error line number
     * @throws \Brickoo\Component\Error\Exception\ErrorOccurredException
     * @return boolean true to block php error message forwarding
     */
    public function handleError($errorCode, $errorMessage, $errorFile, $errorLine) {
        $message = sprintf("%s in %s on line %s", $errorMessage, $errorFile, $errorLine);
        if ($this->convertToException) {
            throw new ErrorOccurredException($message, $errorCode);
        }
        $this->messageDispatcher->dispatch(new ErrorMessage($message));
        return true;
    }

}