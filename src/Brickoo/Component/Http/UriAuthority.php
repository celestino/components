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

use Brickoo\Component\Validation\Argument;

/**
 * UriAuthority
 *
 * Implements the http uri authority part.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class UriAuthority {

    /** @var string */
    private $hostname;

    /** @var integer */
    private $portNumber;

    /**
     * Class constructor.
     * @param string $hostname
     * @param integer $portNumber
     * @return void
     */
    public function __construct($hostname, $portNumber = 80) {
        Argument::IsString($hostname);
        Argument::IsInteger($portNumber);

        $this->hostname = $hostname;
        $this->portNumber = $portNumber;
    }

    /**
     * Returns the hostname.
     * @return string the hostname
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Returns the port number.
     * @return integer port number
     */
    public function getPortNumber() {
        return $this->portNumber;
    }

    /**
     * Returns the authority string representation.
     * @return string the authority representation
     */
    public function toString() {
        return sprintf("%s:%d", $this->hostname, $this->portNumber);
    }

}