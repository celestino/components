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

namespace Brickoo\Http;

use Brickoo\Http\MessageHeader,
    Brickoo\Validator\Argument;

/**
 * Header
 *
 * Implements a http helper class for accept headers.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AcceptHeader {

    /** @var \Brickoo\Http\MessageHeader */
    private $messageHeader;

    /** @var array */
    private $acceptTypes;

    /** @var array */
    private $acceptLanguages;

    /** @var array */
    private $acceptEncodings;

    /** @var array */
    private $acceptCharsets;

    /**
     * Class constructor.
     * Initializes the class variables.
     * @param \Brickoo\Http\MessageHeader $messageHeader
     * @return void
     */
    public function __construct(MessageHeader $messageHeader) {
        $this->messageHeader = $messageHeader;
        $this->acceptTypes = array();
        $this->acceptLanguages = array();
        $this->acceptEncodings = array();
        $this->acceptCharsets = array();
    }

    /**
     * Returns the accept types supported by the request client.
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
     * @param string $neededType the type which is needed if supported
     * @return array the accepted types sorted by priority descending otherwise null
     */
    public function getAcceptTypes() {
        if (empty($this->acceptTypes) && ($acceptHeader = $this->messageHeader->get("Accept"))) {
            $this->acceptTypes = $this->getAcceptHeaderByRegex(
                "~^(?<type>[a-z\/\+\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?~i",
                "type",
                $acceptHeader
            );
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
     * Returns the accept languages supported by the request client.
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
     * @return array the languages sorted by priority descending
     */
    public function getAcceptLanguages() {
        if (empty($this->acceptLanguages) && ($acceptLanguageHeader = $this->messageHeader->get("Accept-Language"))) {
            $this->acceptLanguages = $this->getAcceptHeaderByRegex(
                "~^(?<language>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                "language",
                $acceptLanguageHeader
            );
        }
        return $this->acceptLanguages;
    }

    /**
     * Checks if the passed language is supported.
     * @param string $language the language to check
     * @return boolean check result
     */
    public function isLanguageSupported($language) {
        Argument::IsString($language);
        return array_key_exists($language, $this->getAcceptLanguages());
    }

    /**
     * Returns the accept encodings supported by the request client.
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
     * @return array the encondings sorted by priority descending
     */
    public function getAcceptEncodings() {
        if (empty($this->acceptEncodings) && ($acceptEncodingHeader = $this->messageHeader->get("Accept-Encoding"))) {
            $this->acceptEncodings = $this->getAcceptHeaderByRegex(
                "~^(?<encoding>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                "encoding",
                $acceptEncodingHeader
            );
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
     * Returns the accept charsets supported by the request client.
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2
     * @return array the charsets sorted by priority descending
     */
    public function getAcceptCharsets() {
        if (empty($this->acceptCharsets) && ($acceptEncodingHeader = $this->messageHeader->get("Accept-Charset"))) {
            $this->acceptCharsets = $this->getAcceptHeaderByRegex(
                "~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                "charset",
                $acceptEncodingHeader
            );
        }

        return $this->acceptCharsets;
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
     * Returns the accept header value sorted by quality.
     * @param string $regex the regular expression to use
     * @param string $keyName the key name to assign the quality to
     * @param string $acceptHeader the accept header to retireve the values from
     * @return array the result containing the header values
     */
    private function getAcceptHeaderByRegex($regex, $keyName, $acceptHeader) {
        $results = array();
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