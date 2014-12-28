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

namespace Brickoo\Component\Messaging;

/**
 * Message
 *
 * Defines a message holding corresponding parameters and sender reference.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

interface Message {

    /**
     * Returns the message name.
     * @return string the message name
     */
    public function getName();

    /**
     * Returns the sender object reference which triggered the message.
     * @return object the sender object reference or null if not set
     */
    public function getSender();

    /**
     * Stops the message of been called by other listeners.
     * @return \Brickoo\Component\Messaging\Message
     */
    public function stop();

    /**
     * Checks if the message has been stopped.
     * @return boolean check result
     */
    public function isStopped();

    /**
     * Returns the message parameters.
     * @return array the assigned message parameters
     */
    public function getParams();

    /**
     * Returns the parameter value of the identifier.
     * If the parameter does not exist, the default value is returned.
     * @param string $identifier the identifier to return the value from
     * @param mixed $defaultValue
     * @return mixed the parameter value or null if not set
     */
    public function getParam($identifier, $defaultValue = null);

    /**
     * Set a parameters and its value.
     * @param string $identifier
     * @param mixed $value
     * @return \Brickoo\Component\Messaging\Message
     */
    public function setParam($identifier, $value);

    /**
     * Checks if the identifier is a available message parameter.
     * @param string $identifier the identifier to check the availability
     * @return boolean check result
     */
    public function hasParam($identifier);

    /**
     * Checks if the arguments are available message parameters.
     * Accepts any string arguments to check
     * @return boolean check result
     */
    public function hasParams();

    /**
     * Returns the message response.
     * @throws \Brickoo\Component\Messaging\Exception\ResponseNotAvailableException
     * @return \Brickoo\Component\Messaging\MessageResponseCollection $responseCollection
     */
    public function getResponse();

    /**
     * Sets the message response.
     * @param \Brickoo\Component\Messaging\MessageResponseCollection $response
     * @return \Brickoo\Component\Messaging\Message
     */
    public function setResponse(MessageResponseCollection $response);

}
