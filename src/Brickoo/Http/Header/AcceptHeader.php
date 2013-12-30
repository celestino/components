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
    Brickoo\Validation\Argument;

/**
 * AcceptHeader
 *
 * Implements an accept header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AcceptHeader extends GenericHeader {

    /** @var array */
    private $acceptTypes;

    /**
     * Class constructor.
     * @param string $headerValue
     * @return void
     */
    public function __construct($headerValue = "") {
        Argument::IsString($headerValue);
        $this->headerName = "Accept";
        $this->headerValue = $headerValue;
        $this->acceptTypes = [];
    }

    /**
     * Sets an accept type with its quality.
     * @param string $acceptType
     * @param float $quality
     * @return \Brickoo\Http\Header\AcceptHeader
     */
    public function setAcceptType($acceptType, $quality = 1.0) {
        Argument::IsString($acceptType);
        Argument::IsFloat($quality);

        $this->getAcceptTypes();
        $this->acceptTypes[$acceptType] = $quality;
        $this->buildValue();

        return $this;
    }

    /**
     * Returns the accepted types.
     * @return array the accepted types
     */
    public function getTypes() {
        if (empty($this->acceptTypes)) {
            $this->getAcceptTypes();
        }
        return $this->acceptTypes;
    }

    /**
     * Checks if the passed type is supported.
     * @param string $type the type to check
     * @return boolean check result
     */
    public function isTypeSupported($type) {
        Argument::IsString($type);
        return array_key_exists($type, $this->getAcceptTypes());
    }

    /**
     * Builds the accept header value from the actual types.
     * @return \Brickoo\Http\Header\AcceptHeader
     */
    private function buildValue() {
        if (! empty($this->acceptTypes)) {
            $values = [];
            arsort($this->acceptTypes);
            foreach ($this->acceptTypes as $acceptType => $quality) {
                $values[] = $acceptType.($quality < 1 ? sprintf(";q=%.1f", $quality) : "");
            }
            $this->headerValue = implode(", ", $values);
        }
        return $this;
    }

    /**
     * Returns the accept types supported by the request client.
     * @param string $neededType the type which is needed if supported
     * @return array the accepted types sorted by priority descending otherwise null
     */
    private function getAcceptTypes() {
        if (empty($this->acceptTypes) && ($acceptHeader = $this->getValue())) {
            $this->acceptTypes = $this->getAcceptTypesHeaderByRegex(
                "~^(?<type>[a-z\/\+\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?~i",
                "type",
                $acceptHeader
            );
        }
        return $this->acceptTypes;
    }

    /**
     * Returns the accept header value sorted by quality.
     * @param string $regex the regular expression to use
     * @param string $keyName the key name to assign the quality to
     * @param string $acceptHeader the accept header to retireve the values from
     * @return array the result containing the header values
     */
    private function getAcceptTypesHeaderByRegex($regex, $keyName, $acceptHeader) {
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