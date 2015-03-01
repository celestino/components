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

namespace Brickoo\Component\Error;

use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Error\Messaging\Message\ErrorMessage;
use Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException;
use Brickoo\Component\Error\Exception\ErrorOccurredException;
use Brickoo\Component\Error\Exception\HandlerNotRegisteredException;
use Brickoo\Component\Common\Assert;

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
        Assert::isBoolean($convertToException);
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
        set_error_handler([$this, "handleError"]);
        $this->isRegistered = true;
        return $this;
    }

    /**
     * Unregister the instance as error handler by restoring previous error handler.
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
