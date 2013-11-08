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

namespace Brickoo\Event;

/**
 * Collection
 *
 * Implements a collection containing a collection of event listeners responses.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ResponseCollection implements \Countable {

    /** @var array */
    private $responsesContainer;

    /**
     * Class constructor.
     * @param array $responsesContainer the listeners responses
     * @return void
     */
    public function __construct(array $responsesContainer) {
        $this->responsesContainer = $responsesContainer;
    }

    /**
     * Returns the first response from the collection stack.
     * The response will be removed from the list.
     * @throws \Brickoo\Event\Response\Exceptions\ResponseNotAvailable
     * @return mixed the first collected response
     */
    public function shift() {
        if ($this->isEmpty()) {
            throw new Exceptions\ResponseNotAvailable();
        }
        return array_shift($this->responsesContainer);
    }

    /**
     * Returns the last response from the collection stack.
     * The response will be removed from the list.
     * @throws \Brickoo\Event\Response\Exceptions\ResponseNotAvailable
     * @return mixed the last collected response
     */
    public function pop() {
        if ($this->isEmpty()) {
            throw new Exceptions\ResponseNotAvailable();
        }
        return array_pop($this->responsesContainer);
    }

    /**
     * Returns all listened responses.
     * @throws \Brickoo\Event\Response\Exceptions\ResponseNotAvailable
     * @return array the collected responses
     */
    public function getAll() {
        if ($this->isEmpty()) {
            throw new Exceptions\ResponseNotAvailable();
        }
        return $this->responsesContainer;
    }

    /**
     * Checks if the collection has responses.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->responsesContainer);
    }

    /** {@inheritDoc} */
    public function count() {
        return count($this->responsesContainer);
    }

}