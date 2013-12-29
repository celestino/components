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
 * AcceptCharsetHeader
 *
 * Implements an accept-charset header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AcceptCharsetHeader extends GenericHeader {

    /** @var array */
    private $acceptCharsets;

    /**
     * Class constructor.
     * @param string $headerValue
     * @return void
     */
    public function __construct($headerValue = "") {
        Argument::IsString($headerValue);
        $this->headerName = "Accept-Charset";
        $this->headerValue = $headerValue;
        $this->acceptCharsets = [];
    }

    /**
     * Returns the accepted charsets.
     * @return array the accepted charsets
     */
    public function getCharsets() {
        if (empty($this->acceptCharsets)) {
            $this->getAcceptCharsets();
        }
        return $this->acceptCharsets;
    }

    /**
     * Sets an accepted charset with its quality.
     * @param string $acceptCharset
     * @param float $quality
     * @return \Brickoo\Http\Header\AcceptCharsetHeader
     */
    public function setCharset($acceptCharset, $quality = 1.0) {
        Argument::IsString($acceptCharset);
        Argument::IsFloat($quality);

        $this->getCharsets();
        $this->acceptCharsets[$acceptCharset] = $quality;
        $this->buildValue();

        return $this;
    }

    /**
     * Checks if the passed encoding type is supported.
     * @param string $charset the charset to check
     * @return boolean check result
     */
    public function isCharsetSupported($charset) {
        Argument::IsString($charset);
        return array_key_exists($charset, $this->getAcceptCharsets());
    }

    /**
     * Builds the header value by the accepted charsets.
     * @return \Brickoo\Http\Header\AcceptCharsetHeader
     */
    private function buildValue() {
        if (! empty($this->acceptCharsets)) {
            $values = [];
            arsort($this->acceptCharsets);
            foreach ($this->acceptCharsets as $charset => $quality) {
                $values[] = $charset.($quality < 1 ? sprintf(";q=%.1f", $quality) : "");
            }
            $this->headerValue = implode(", ", $values);
        }
        return $this;
    }

    /**
     * Returns the accept charsets supported by the request client.
     * @return array the charsets sorted by priority descending
     */
    private function getAcceptCharsets() {
        if (empty($this->acceptCharsets) && ($acceptEncodingHeader = $this->getValue())) {
            $this->acceptCharsets = $this->getAcceptHeaderByRegex(
                "~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                "charset",
                $acceptEncodingHeader
            );
        }

        return $this->acceptCharsets;
    }

    /**
     * Returns the accept header value sorted by quality.
     * @param string $regex the regular expression to use
     * @param string $keyName the key name to assign the quality to
     * @param string $acceptHeader the accept header to retireve the values from
     * @return array the result containing the header values
     */
    private function getAcceptHeaderByRegex($regex, $keyName, $acceptHeader) {
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