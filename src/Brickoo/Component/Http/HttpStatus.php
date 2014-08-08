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

namespace Brickoo\Component\Http;

use Brickoo\Component\Http\Exception\InvalidHttpStatusException,
    Brickoo\Component\Validation\Argument;

/**
 * HttpStatus
 *
 * Describes the http status.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

Class HttpStatus extends HttpStatusCode {

    /** @var string */
    private $status;

    /**
     * Class constructor.
     * @param integer $status the http status
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Exception\InvalidHttpStatusException
     */
    public function __construct($status) {
        Argument::IsInteger($status);

        if (! $this->isValid($status)) {
            throw new InvalidHttpStatusException($status);
        }

        $this->status = $status;
    }

    /**
     * Returns the status code.
     * @return integer the status code
     */
    public function getCode() {
        return $this->status;
    }

    /**
     * Returns the string representation of the http status.
     * @return string the status representation
     */
    public function toString() {
        return sprintf("%d %s", $this->status, $this->getPhrase($this->status));
    }

    /**
     * Checks if the status is valid.
     * @param string $status
     * @return boolean check result
     */
    private function isValid($status) {
        return $this->hasPhrase($status);
    }

}
