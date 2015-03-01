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

namespace Brickoo\Component\Log\Messaging\Listener;

use Brickoo\Component\Log\Logger;
use Brickoo\Component\Log\Messaging\Messages;
use Brickoo\Component\Log\Messaging\Message\LogMessage;
use Brickoo\Component\Messaging\Listener;
use Brickoo\Component\Messaging\Message;
use Brickoo\Component\Messaging\MessageDispatcher;
use Brickoo\Component\Common\Assert;

/**
 * LogMessageListener
 *
 * Implements a log message listener.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class LogMessageListener implements Listener {

    /** @var \Brickoo\Component\Log\Logger */
    protected $logger;

    /** @var integer */
    protected $listenerPriority;

    /**
     * @param \Brickoo\Component\Log\Logger $logger
     * @param integer $priority the priority level
     */
    public function __construct(Logger $logger, $priority = 0) {
        Assert::isInteger($priority);
        $this->logger = $logger;
        $this->listenerPriority = $priority;
    }

    /** {@inheritDoc} */
    public function getMessageName() {
        return Messages::LOG;
    }

    /** {@inheritDoc} */
    public function getPriority() {
        return $this->listenerPriority;
    }

    /** {@inheritDoc} */
    public function handleMessage(Message $message, MessageDispatcher $messageDispatcher) {
        if ($message instanceof LogMessage) {
            $this->logger->log($message->getMessages(), $message->getSeverity());
        }
    }

}
