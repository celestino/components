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

namespace Brickoo\Event;

use Brickoo\Memory\Container,
    Brickoo\Validation\Argument;

/**
 * EventRecursionDepthList
 *
 * Implements a list for handling events recursion depth.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class EventRecursionDepthList extends Container {

    /** @var integer */
    private $recursionDepthLimit;

    /**
     * Class constructor.
     * @param integer $recursionDepthLimit default 5
     * @return void
     */
    public function __construct($recursionDepthLimit = 5) {
        Argument::IsInteger($recursionDepthLimit);
        $this->recursionDepthLimit = $recursionDepthLimit;
        parent::__construct();
    }

    /**
     * Adds an unique event to the list.
     * @param string $eventName
     * @return \Brickoo\Event\EventRecursionDepthList
     */
    public function addEvent($eventName) {
        Argument::IsString($eventName);
        $this->set($eventName, 0);
        return $this;
    }

    /**
     * Returns the current event recursion depth.
     * @param string $eventName
     * @return integer the event recursion depth
     */
    public function getRecursionDepth($eventName) {
        Argument::IsString($eventName);
        return $this->get($eventName, 0);
    }

    /**
     * Checks if the event recursion depth limit has been reached.
     * @param string $eventName
     * @return boolean check result
     */
    public function isDepthLimitReached($eventName) {
        Argument::IsString($eventName);
        return $this->has($eventName)
            && $this->get($eventName) >= $this->recursionDepthLimit;
    }

    /**
     * Increases the current event recursion depth.
     * @param string $eventName
     * @return \Brickoo\Event\EventRecursionList
     */
    public function increaseDepth($eventName) {
        Argument::IsString($eventName);
        $depth = $this->getRecursionDepth($eventName);
        $this->set($eventName, ++$depth);
        return $this;
    }

    /**
     * Decreases the current event recursion depth.
     * @param string $eventName
     * @return \Brickoo\Event\EventRecursionList
     */
    public function decreaseDepth($eventName) {
        Argument::IsString($eventName);
        $depth = $this->getRecursionDepth($eventName);
        $this->set($eventName, --$depth);
        return $this;
    }

}