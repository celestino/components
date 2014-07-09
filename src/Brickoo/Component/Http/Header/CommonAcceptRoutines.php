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

/**
 * CommonAcceptRoutines
 *
 * Implements common accept header routines.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

trait CommonAcceptRoutines {

    /**
     * Builds on header value from the header values.
     * @param array $headerValues
     * @return string the header value representation
     */
    private function buildValue(array $headerValues) {
        $headerValue = "";
        if (! empty($headerValues)) {
            $values = [];
            arsort($headerValues);
            foreach ($headerValues as $value => $quality) {
                $values[] = $value.($quality < 1 ? sprintf(";q=%.1f", $quality) : "");
            }
            $headerValue = implode(", ", $values);
        }
        return $headerValue;
    }

    /**
     * Returns the header values supported by the request client.
     * @param string $headerValue
     * @return array the header values sorted by priority
     */
    private function getHeaderValues($headerValue) {
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
            if (preg_match($regex, trim($field), $matches) && isset($matches["value"])) {
                $matches["quality"] = (isset($matches["quality"]) ? $matches["quality"] : 1);
                $results[trim($matches["value"])] = (float)$matches["quality"];
            }
        }

        arsort($results);
        return $results;
    }

}
