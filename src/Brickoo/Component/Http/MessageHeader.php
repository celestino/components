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

use Brickoo\Component\Http\Exception\HeaderNotFoundException,
    Brickoo\Component\Memory\Container,
    Brickoo\Component\Validation\Argument;

/**
 * MessageHeader
 *
 * Implements a http message header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageHeader extends Container {

    /**
     * Sets a header using the header name as storage key.
     * @param \Brickoo\Component\Http\HttpHeader $header
     * @return \Brickoo\Component\Http\MessageHeader
     */
    public function setHeader(HttpHeader $header) {
        $this->set($header->getName(), $header);
        return $this;
    }

    /**
     * Checks if the header is available.
     * @param string $headerName
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function hasHeader($headerName) {
        Argument::IsString($headerName);
        return $this->has($headerName);
    }

    /**
     * Returns the header by its name.
     * @param string $headerName
     * @throws \Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @return \Brickoo\Component\Http\HttpHeader
     */
    public function getHeader($headerName) {
        Argument::IsString($headerName);
        if (! $this->hasHeader($headerName)) {
            throw new HeaderNotFoundException($headerName);
        }
        return $this->get($headerName);
    }

    /**
     * Removes the header by its name.
     * @param string $headerName
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Exception\HeaderNotFoundException
     * @return \Brickoo\Component\Http\MessageHeader
     */
    public function removeHeader($headerName) {
        Argument::IsString($headerName);
        if (! $this->hasHeader($headerName)) {
            throw new HeaderNotFoundException($headerName);
        }

        $this->delete($headerName);
        return $this;
    }

    /**
     * Coverts message headers to a request header string.
     * @return string the representation of the message headers
     */
    public function toString() {
        $headerString = "";

        $headers = $this->normalizeHeaders($this->toArray());
        foreach($headers as $key => $value) {
            $headerString .= sprintf("%s: %s\r\n", $key, $value);
        }

        return $headerString;
    }

    /**
     * Transforms the headers to an array of key/value pairs.
     * @override Container::toArray
     * @return array the transformed headers
     */
    public function toArray() {
        $aggregatedHeaders = [];

        $headers = $this->getIterator();
        foreach ($headers as $header) {
            $aggregatedHeaders[$header->getName()] = $header->getValue();
        }

        return $aggregatedHeaders;
    }

    /**
     * Normalizes the headers keys.
     * @param array $headers the headers to normalized
     * @return array the normalized headers
     */
    private function normalizeHeaders(array $headers) {
        $normalizedHeaders = [];

        foreach ($headers as $headerName => $headerValue) {
            $headerName = str_replace(" ", "-", ucwords(
                strtolower(str_replace(array("_", "-"), " ", $headerName))
            ));
            $normalizedHeaders[$headerName] = $headerValue;
        }

        ksort($normalizedHeaders);
        return $normalizedHeaders;
    }

}