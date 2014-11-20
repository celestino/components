<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\Messaging;

use Brickoo\Component\Validation\Argument;

/**
 * MessageListener
 *
 * Implements a generic message listener.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageListener implements Listener {

    /** @var string */
    private $messageName;

    /** @var integer */
    private $priority;

    /** @var callable */
    private $callback;

    /**
     * @param string $messageName
     * @param integer $priority
     * @param callable $callback
     */
    public function __construct($messageName, $priority, callable $callback) {
        Argument::isString($messageName);
        Argument::isInteger($priority);

        $this->messageName = $messageName;
        $this->priority = $priority;
        $this->callback = $callback;
    }

    /** {@inheritDoc} */
    public function getMessageName() {
        return $this->messageName;
    }

    /** {@inheritDoc} */
    public function getPriority() {
        return $this->priority;
    }

    /** {@inheritDoc} */
    public function handleMessage(Message $message, MessageDispatcher $dispatcher) {
        call_user_func_array($this->callback, [$message, $dispatcher]);
    }

}
