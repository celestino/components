<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
