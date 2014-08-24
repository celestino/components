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

use Brickoo\Component\Http\Exception\HeaderNotFoundException;
use Brickoo\Component\Common\Container;
use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Validation\Constraint\IsInstanceOfConstraint;
use Brickoo\Component\Validation\Validator\ConstraintValidator;

/**
 * HttpMessageHeader
 *
 * Implements a http message header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpMessageHeader extends Container {

    use HttpHeaderNormalizer;

    /** @param array $headerLists */
    public function __construct(array $headerLists = []) {
        parent::__construct($headerLists, new ConstraintValidator(
            new IsInstanceOfConstraint("\\Brickoo\\Component\\Http\HttpHeaderList")
        ));
    }

    /**
     * Add a header using the header name as storage key.
     * @param \Brickoo\Component\Http\HttpHeader $header
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    public function addHeader(HttpHeader $header) {
        $headerName = $header->getName();

        if (! $this->contains($headerName)) {
            $this->set($headerName, new HttpHeaderList());
        }

        $this->get($headerName)->add($header);
        return $this;
    }

    /**
     * Return the header by its name.
     * The first header from the concrete header list will be returned.
     * @param string $headerName
     * @throws \Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @return \Brickoo\Component\Http\HttpHeader
     */
    public function getHeader($headerName) {
        return $this->getHeaderList($headerName)->first();
    }

    /**
     * Return the header list of a header.
     * @param string $headerName
     * @throws \Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @return \Brickoo\Component\Http\HttpHeaderList
     */
    public function getHeaderList($headerName) {
        Argument::isString($headerName);
        if (! $this->contains($headerName)) {
            throw new HeaderNotFoundException($headerName);
        }
        return $this->get($headerName);
    }

    /**
     * Covert message headers to a header string.
     * @return string the representation of the message headers
     */
    public function toString() {
        $headerString = "";

        $headerLists = $this->normalizeHeaders($this->toArray());
        foreach($headerLists as $headerList) {
            $headerString .= $headerList->toString();
        }

        return $headerString;
    }

}
