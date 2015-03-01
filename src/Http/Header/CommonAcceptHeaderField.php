<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

use Brickoo\Component\Common\ArrayList;
use Brickoo\Component\Http\HttpHeaderField;
use Brickoo\Component\Common\Assert;

/**
 * CommonAcceptHeaderField
 *
 * Implements common accept header field routines.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class CommonAcceptHeaderField  implements HttpHeaderField {

    use CommonHeaderFieldStructure;

    /** @var null|array */
    private $headerFieldValueList = null;

    /**
     * Class constructor.
     * @param string $headerFieldName
     * @param string $headerFieldValue
     * @throws \InvalidArgumentException
     */
    public function __construct($headerFieldName, $headerFieldValue) {
        $this->setName($headerFieldName);
        $this->setValue($headerFieldValue);
    }

    /**
     * Check if the accept key is accepted.
     * @param string $acceptKey
     * @return boolean check result
     */
    public function isAccepted($acceptKey) {
        Assert::isString($acceptKey);
        return array_key_exists($acceptKey, $this->getFieldValuesList());
    }

    /**
     * Return a reverse ordered list by quality
     * containing the field values.
     * @return ArrayList
     */
    public function getValuesList() {
        return new ArrayList(array_keys($this->getFieldValuesList()));
    }

    /**
     * Returns the header field value list accepted.
     * @return array
     */
    private function getFieldValuesList() {
        if ($this->headerFieldValueList === null) {
            $this->headerFieldValueList = $this->getSortedFieldValuesByQuality(
                "~^(?<value>[a-z\\/\\+\\-\\*0-9]+)\\s*(\\;\\s*q\\=(?<quality>(0\\.\\d{1,5}|1\\.0|[01])))?~i",
                $this->getValue()
            );
        }
        return $this->headerFieldValueList;
    }

    /**
     * Returns the header field values sorted by quality.
     * @param string $regex the regular expression to use
     * @param string $headerFieldValue
     * @return array
     */
    private function getSortedFieldValuesByQuality($regex, $headerFieldValue) {
        $results = [];
        $fields = explode(",", $headerFieldValue);

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
