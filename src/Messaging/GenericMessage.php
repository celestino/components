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
 * Implements a generic message which can be used or extended by any component.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class GenericMessage implements Message {

    /** @var string */
    private $name;

    /** @var array */
    private $params;

    /** @var object */
    private $sender;

    /** @var boolean */
    private $stopped;

    /** @var \Brickoo\Component\Messaging\MessageResponseCollection */
    private $response;

    /**
     * @param string $name the message name
     * @param null|object $sender the sender object
     * @param array $parameters the parameters to add to the message
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $sender = null, array $parameters = []) {
        Argument::isString($name);

        if ($sender !== null) {
            Argument::isObject($sender);
        }

        $this->name = $name;
        $this->sender = $sender;
        $this->params = $parameters;
        $this->stopped = false;
        $this->response = new MessageResponseCollection();
    }

    /** {@inheritDoc} */
    public function getSender() {
        return $this->sender;
    }

    /** {@inheritDoc} */
    public function stop() {
        $this->stopped = true;
        return $this;
    }

    /** {@inheritDoc} */
    public function isStopped() {
        return ($this->stopped === true);
    }

    /** {@inheritDoc} */
    public function getName() {
        return $this->name;
    }

    /** {@inheritDoc} */
    public function getParams() {
        return $this->params;
    }

    /** {@inheritDoc} */
    public function setParam($identifier, $value) {
        Argument::isString($identifier);
        $this->params[$identifier] = $value;
        return $this;
    }

    /** {@inheritDoc} */
    public function getParam($identifier, $defaultValue = null) {
        Argument::isString($identifier);

        if (! $this->hasParam($identifier)) {
            return $defaultValue;
        }

        return $this->params[$identifier];
    }

    /** {@inheritDoc} */
    public function hasParam($identifier) {
        Argument::isString($identifier);
        return isset($this->params[$identifier]);
    }

    /** {@inheritDoc} */
    public function hasParams() {
        $containsAllParameters = true;
        if (($arguments = func_get_args())) {
            foreach ($arguments as $argument) {
                if (! $this->hasParam($argument)) {
                    $containsAllParameters = false;
                    break;
                }
            }
        }
        return $containsAllParameters;
    }

    /** {@inheritDoc} */
    public function getResponse() {
        return $this->response;
    }

    /** {@inheritDoc} */
    public function setResponse(MessageResponseCollection $response) {
        $this->response = $response;
        return $this;
    }

}
