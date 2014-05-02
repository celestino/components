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

namespace Brickoo\Component\Messaging;

use Brickoo\Component\Common\Container,
    Brickoo\Component\Validation\Argument;

/**
 * MessageRecursionDepthList
 *
 * Implements a list for handling message recursion depth.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageRecursionDepthList extends Container {

    /** @var integer */
    private $recursionDepthLimit;

    /**
     * Class constructor.
     * @param integer $recursionDepthLimit default 5
     */
    public function __construct($recursionDepthLimit = 5) {
        Argument::IsInteger($recursionDepthLimit);
        $this->recursionDepthLimit = $recursionDepthLimit;
        parent::__construct();
    }

    /**
     * Adds an unique message to the list.
     * @param string $messageName
     * @return \Brickoo\Component\Messaging\MessageRecursionDepthList
     */
    public function addMessage($messageName) {
        Argument::IsString($messageName);
        $this->set($messageName, 0);
        return $this;
    }

    /**
     * Returns the current message recursion depth.
     * @param string $messageName
     * @return integer the message recursion depth
     */
    public function getRecursionDepth($messageName) {
        Argument::IsString($messageName);
        return $this->get($messageName, 0);
    }

    /**
     * Checks if the message recursion depth limit has been reached.
     * @param string $messageName
     * @return boolean check result
     */
    public function isDepthLimitReached($messageName) {
        Argument::IsString($messageName);
        return $this->contains($messageName)
            && $this->get($messageName) >= $this->recursionDepthLimit;
    }

    /**
     * Increases the current message recursion depth.
     * @param string $messageName
     * @return \Brickoo\Component\Messaging\MessageRecursionDepthList
     */
    public function increaseDepth($messageName) {
        Argument::IsString($messageName);
        $depth = $this->getRecursionDepth($messageName);
        $this->set($messageName, ++$depth);
        return $this;
    }

    /**
     * Decreases the current message recursion depth.
     * @param string $messageName
     * @return \Brickoo\Component\Messaging\MessageRecursionDepthList
     */
    public function decreaseDepth($messageName) {
        Argument::IsString($messageName);
        $depth = $this->getRecursionDepth($messageName);
        $this->set($messageName, --$depth);
        return $this;
    }

}
