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

use Brickoo\Component\Error\Messaging\Message\ExceptionMessage;
use Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException;
use Brickoo\Component\Error\Exception\HandlerNotRegisteredException;
use Brickoo\Component\Messaging\MessageDispatcher;

/**
 * ExceptionHandler
 *
 * Handles user defined or system exception.
 * Exceptions can be logged through the log message which is triggered if exceptions occurred.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ExceptionHandler {

    /** @var boolean */
    private $isRegistered;

    /** @var \Brickoo\Component\Messaging\MessageDispatcher */
    private $messageDispatcher;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Messaging\MessageDispatcher $messageDispatcher
     */
    public function __construct(MessageDispatcher $messageDispatcher) {
        $this->messageDispatcher = $messageDispatcher;
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
     * @throws \Brickoo\Component\Error\Exception\DuplicateHandlerRegistrationException
     * @return \Brickoo\Component\Error\ExceptionHandler
     */
    public function register() {
        if ($this->isRegistered()) {
            throw new DuplicateHandlerRegistrationException("ExceptionHandler");
        }
        set_exception_handler([$this, "handleException"]);
        $this->isRegistered = true;
        return $this;
    }

    /**
     * Unregister the instance as exception handler by restoring previous exception handler.
     * @throws \Brickoo\Component\Error\Exception\HandlerNotRegisteredException
     * @return \Brickoo\Component\Error\ExceptionHandler
     */
    public function unregister() {
        if (!$this->isRegistered()) {
            throw new HandlerNotRegisteredException("ExceptionHandler");
        }
        restore_exception_handler();
        $this->isRegistered = false;
        return $this;
    }

    /**
     * Handles the exception thrown by the user or system.
     * Dispatch a log message containing the exception message.
     * @param \Exception $exception the exception thrown
     * @return \Brickoo\Component\Error\ExceptionHandler
     */
    public function handleException(\Exception $exception) {
        $this->messageDispatcher->dispatch(new ExceptionMessage($exception));
        return $this;
    }

}
