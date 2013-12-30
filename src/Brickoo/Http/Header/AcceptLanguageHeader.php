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
 * AcceptLanguageHeader
 *
 * Implements an accept-language header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AcceptLanguageHeader extends GenericHeader {

    /** @var array */
    private $acceptLanguages;

    /**
     * Class constructor.
     * @param string $headerValue
     * @return void
     */
    public function __construct($headerValue = "") {
        Argument::IsString($headerValue);
        $this->headerName = "Accept-Language";
        $this->headerValue = $headerValue;
        $this->acceptLanguages = [];
    }

    /**
     * Sets an accepted language.
     * @param string $language
     * @param float $quality
     * @return \Brickoo\Http\Header\AcceptLanguageHeader
     */
    public function setLanguage($language, $quality = 1.0) {
        Argument::IsString($language);
        Argument::IsFloat($quality);

        $this->getLanguages();
        $this->acceptLanguages[$language] = $quality;
        $this->buildValue();

        return $this;
    }

    /**
     * Returns the accepted languages.
     * @return array the accepted languages
     */
    public function getLanguages() {
        if (empty($this->acceptLanguages)) {
            $this->getAcceptedLanguages();
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
        return array_key_exists($language, $this->getAcceptedLanguages());
    }

    /**
     * Builds the value from the actual accepted languages.
     * @return \Brickoo\Http\Header\AcceptLanguageHeader
     */
    private function buildValue() {
        if (! empty($this->acceptLanguages)) {
            $values = [];
            arsort($this->acceptLanguages);
            foreach ($this->acceptLanguages as $language => $quality) {
                $values[] = $language.($quality < 1 ? sprintf(";q=%.1f", $quality) : "");
            }
            $this->headerValue = implode(", ", $values);
        }
        return $this;
    }

    /**
     * Returns the accept languages supported by the request client.
     * @return array the languages sorted by priority descending
     */
    private function getAcceptedLanguages() {
        if (empty($this->acceptLanguages) && ($acceptLanguageHeader = $this->getValue())) {
            $this->acceptLanguages = $this->getAcceptLanguageHeaderByRegex(
                "~^(?<language>[a-z\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i",
                "language",
                $acceptLanguageHeader
            );
        }
        return $this->acceptLanguages;
    }

    /**
     * Returns the accept header value sorted by quality.
     * @param string $regex the regular expression to use
     * @param string $keyName the key name to assign the quality to
     * @param string $acceptLanguageHeader the accept header to retrieve the values from
     * @return array the result containing the header values
     */
    private function getAcceptLanguageHeaderByRegex($regex, $keyName, $acceptLanguageHeader) {
        $results = [];
        $fields = explode(",", $acceptLanguageHeader);

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