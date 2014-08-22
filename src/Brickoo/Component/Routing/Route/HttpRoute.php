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

namespace Brickoo\Component\Routing\Route;

use Brickoo\Component\Validation\Argument;

/**
 * HttpRoute
 *
 * Implements a http route which can be configured to match http requests.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpRoute extends GenericRoute {

    /** @var string */
    private $method;

    /** @var string */
    private $scheme;

    /** @var string */
    private $hostname;

    /**
     * Returns the http method(s) allowed listening to.
     * @return string the http methods allowed as a regular expression
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Sets the route method listening to.
     * @param string $method
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Routing\Route\HttpRoute
     */
    public function setMethod($method) {
        Argument::IsString($method);
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Returns the scheme allowed listening to.
     * @return string the scheme allowed as a regular expression
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * Sets the scheme required.
     * @param string $scheme
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Routing\Route\HttpRoute
     */
    public function setScheme($scheme) {
        Argument::IsString($scheme);
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * Returns the hostname(s) allowed listening to.
     * @return string the host names allowed as a regular expression
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Sets the hostname listening to.
     * @param string $hostname
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Routing\Route\HttpRoute
     */
    public function setHostname($hostname) {
        Argument::IsString($hostname);
        $this->hostname = $hostname;
        return $this;
    }

}
