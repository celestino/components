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

namespace Brickoo\Http\Header;

use Brickoo\Http\Header\GenericHeader,
    Brickoo\Validator\Argument;

/**
 * AcceptEncodingHeader
 *
 * Implements an accept-encoding header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AcceptEncodingHeader extends GenericHeader {

    /** @var array */
    private $acceptEncodings;

    /**
     * Class constructor.
     * @param string $headerValue
     * @return void
     */
    public function __construct($headerValue = "") {
        Argument::IsString($headerValue);
        $this->headerName = "Accept-Encoding";
        $this->headerValue = $headerValue;
        $this->acceptEncodings = [];
    }

    /**
     * Sets an accepted encoding with its quality.
     * @param string $acceptEncoding
     * @param float $quality
     * @return \Brickoo\Http\Header\AcceptEncodingHeader
     */
    public function setEncondig($acceptEncoding, $quality = 1.0) {
        Argument::IsString($acceptEncoding);
        Argument::IsFloat($quality);

        $this->getEncodings();
        $this->acceptEncodings[$acceptEncoding] = $quality;
        $this->buildValue();

        return $this;
    }

    /**
     * Returns the accepted encodings.
     * @return array the accepted encodings
     */
    public function getEncodings() {
        if (empty($this->acceptEncodings)) {
            $this->getAcceptEncodings();
        }
        return $this->acceptEncodings;
    }

    /**
     * Checks if the passed encoding type is supported.
     * @param string $encoding the encoding type to check
     * @return boolean check result
     */
    public function isEncodingSupported($encoding) {
        Argument::IsString($encoding);
        return array_key_exists($encoding, $this->getAcceptEncodings());
    }

    /**
     * Builds the header value by the accepted encodings.
     * @return \Brickoo\Http\Header\AcceptEncodingHeader
     */
    private function buildValue() {
        if (! empty($this->acceptEncodings)) {
            $values = [];
            arsort($this->acceptEncodings);
            foreach ($this->acceptEncodings as $acceptEncoding => $quality) {
                $values[] = $acceptEncoding.($quality < 1 ? sprintf(";q=%.1f", $quality) : "");
            }
            $this->headerValue = implode(", ", $values);
        }
        return $this;
    }

    /**
     * Returns the accept encodings supported by the request client.
     * @return array the encondings sorted by priority descending
     */
    private function getAcceptEncodings() {
        if (empty($this->acceptEncodings) && ($acceptEncodingHeader = $this->getValue())) {
            $this->acceptEncodings = $this->getAcceptEncodingsHeaderByRegex(
                "~^(?<encoding>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                "encoding",
                $acceptEncodingHeader
            );
        }
        return $this->acceptEncodings;
    }

    /**
     * Returns the accept encoding header value sorted by quality.
     * @param string $regex the regular expression to use
     * @param string $keyName the key name to assign the quality to
     * @param string $acceptHeader the accept header to retireve the values from
     * @return array the result containing the header values
     */
    private function getAcceptEncodingsHeaderByRegex($regex, $keyName, $acceptHeader) {
        $results = [];
        $fields = explode(",", $acceptHeader);

        foreach ($fields as $field) {
            if (preg_match($regex, trim($field), $matches) && isset($matches[$keyName])) {
                $matches["quality"] = (isset($matches["quality"]) ? $matches["quality"] : 1);
                $results[trim($matches[$keyName])] = (float)$matches["quality"];
            }
        }

        arsort($results);
        return $results;
    }

}