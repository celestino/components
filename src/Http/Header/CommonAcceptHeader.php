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

namespace Brickoo\Component\Http\Header;

use Brickoo\Component\Http\HttpHeader;
use Brickoo\Component\Validation\Argument;

/**
 * CommonAcceptHeader
 *
 * Implements common accept header routines.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CommonAcceptHeader  implements HttpHeader {

    use CommonHeaderStructure;

    /** @var array */
    private $listEntries;

    /**
     * Class constructor.
     * @param string $headerName
     * @param string $headerValue
     * @throws \InvalidArgumentException
     */
    public function __construct($headerName, $headerValue) {
        $this->setName($headerName);
        $this->setValue($headerValue);
        $this->listEntries = $this->getHeaderListEntries($headerValue);
    }

    /**
     * Set a header list entry.
     * @param string $key
     * @param float $quality
     * @return \Brickoo\Component\Http\HttpHeader
     */
    public function setEntry($key, $quality = 1.0) {
        Argument::isString($key);
        Argument::isFloat($quality);

        $this->listEntries[$key] = $quality;
        return $this;
    }

    /**
     * Return the header list entries.
     * @return array sorted header list
     */
    public function getEntries() {
        return $this->listEntries;
    }

    /**
     * Check if the accept key is supported.
     * @param string $acceptKey
     * @return boolean check result
     */
    public function isSupported($acceptKey) {
        Argument::isString($acceptKey);
        return array_key_exists($acceptKey, $this->getEntries());
    }

    /** {@inheritdoc} */
    public function getValue() {
        $values = [];
        $headerValues = $this->getEntries();
        arsort($headerValues);
        foreach ($headerValues as $value => $quality) {
            $values[] = $value.($quality < 1 ? sprintf(";q=%.1f", $quality) : "");
        }
        $this->setValue(implode(",", $values));
        return $this->headerValue;
    }

    /**
     * Returns the header list entries supported by the request client.
     * @param string $headerValue
     * @return array the list entries sorted by priority
     */
    private function getHeaderListEntries($headerValue) {
        return $this->getExtractedHeaderValuesByRegex(
            "~^(?<value>[a-z\\/\\+\\-\\*0-9]+)\\s*(\\;\\s*q\\=(?<quality>(0\\.\\d{1,5}|1\\.0|[01])))?~i",
            $headerValue
        );
    }

    /**
     * Returns the header values sorted by quality.
     * @param string $regex the regular expression to use
     * @param string $headerValue
     * @return array the extracted header values
     */
    private function getExtractedHeaderValuesByRegex($regex, $headerValue) {
        $results = [];
        $fields = explode(",", $headerValue);

        foreach ($fields as $field) {
            $matches = array();
            if (preg_match($regex, trim($field), $matches)) {
                $results[trim($matches["value"])] = (float)(isset($matches["quality"]) ? $matches["quality"] : 1);
            }
        }

        arsort($results);
        return $results;
    }

}
