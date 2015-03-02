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

/**
 * ContentTypeHeader
 *
 * Implements a content type header field.
 * @link http://tools.ietf.org/html/rfc2616#section-14.17
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ContentTypeHeaderField extends GenericHeaderField {

    const KEY_TYPE = "type";
    const KEY_CHARSET = "charset";

    /**
     * Class constructor.
     * @param string $headerFieldValue
     */
    public function __construct($headerFieldValue) {
        $this->setName("Content-Type");
        $this->setValue(strtolower($headerFieldValue));
    }

    /**
     * Return the content media type.
     * @return string
     */
    public function getType() {
        return $this->getHeaderPartValue(self::KEY_TYPE);
    }

    /**
     * Return the charset parameter.
     * @return string
     */
    public function getCharset() {
        return $this->getHeaderPartValue(self::KEY_CHARSET);
    }

    /**
     * Return the header field part value.
     * @param string $key
     * @return string
     */
    private function getHeaderPartValue($key) {
        $matches = [];
        preg_match(
            sprintf(
                "~^(?<%s>[a-z\\/\\+\\-\\*0-9]+)\\s*(;\\s*charset=(?<%s>.*))?$~i",
                self::KEY_TYPE,
                self::KEY_CHARSET
            ),
            $this->getValue(),
            $matches
        );
        return isset($matches[$key]) ? $matches[$key] : "";
    }

}
