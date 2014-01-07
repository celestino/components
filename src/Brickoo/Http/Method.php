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
 * 2. Redistributionscd ..
 *  in binary form must reproduce the above copyright
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

namespace Brickoo\Http;

use Brickoo\Http\Exception\InvalidHttpMethodException,
    Brickoo\Validation\Argument;

/**
 * Method
 *
 * Describes a http method.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

Class Method {

    /** http methods */
    const HEAD = "HEAD";
    const OPTIONS = "OPTIONS";
    const TRACE = "TRACE";
    const CONNECT = "CONNECT";
    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const PATCH = "PATCH";
    const DELETE = "DELETE";


    /** @var string */
    private $method;

    /**
     * Class constructor
     * @param string $method the http method
     * @return void
     */
    public function __construct($method) {
        Argument::IsString($method);

        if (! $this->isValid($method)) {
            throw new InvalidHttpMethodException($method);
        }

        $this->method = $method;
    }

    /**
     * Returns the method string representation in uppercase.
     * return string the method representation
     */
    public function toString() {
        return $this->method;
    }

    private function isValid($method) {
        return defined("static::".$method);
    }

}