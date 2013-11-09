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
    Brickoo\Event\EventDispatcher;

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

    /** @var \Brickoo\Event\EventDispatcher */
    private $eventDispatcher;

    /**
     * Class constructor.
     * @param \Brickoo\Event\EventDispatcher $eventDispatcher
     * @return void
     */
    public function __construct(EventDispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
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
     * @throws \Brickoo\Error\Exception\DuplicateHandlerRegistration
     * @return \Brickoo\Error\ExceptionHandler
     */
    public function register() {
        if ($this->isRegistered()) {
            throw new Exception\DuplicateHandlerRegistrationException("ExceptionHandler");
        }

        set_exception_handler(array($this, "handleException"));
        $this->isRegistered = true;

        return $this;
    }

    /**
     * Unregisters the instance as exception handler by restoring previous exception handler.
     * @throws \Brickoo\Error\Exception\HandlerNotRegisteredException
     * @return \Brickoo\Error\ExceptionHandler
     */
    public function unregister() {
        if (! $this->isRegistered()) {
            throw new Exception\HandlerNotRegisteredException("ExceptionHandler");
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
     * @return \Brickoo\Error\ExceptionHandler
     */
    public function handleException(\Exception $exception) {
        $this->eventDispatcher->notify(new ExceptionEvent($exception));
        return $this;
    }

}