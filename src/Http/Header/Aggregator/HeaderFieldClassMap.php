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

namespace Brickoo\Component\Http\Header\Aggregator;

use Brickoo\Component\Http\Header\Aggregator\Exception\HeaderFieldClassNotFoundException;
use Brickoo\Component\Common\Assert;

/**
 * HeaderFieldClassMap
 *
 * Implements a header field class map.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HeaderFieldClassMap {

    /** @var array */
    protected $map = [
        "Accept" => "AcceptHeaderField",
        "Accept-Charset" => "AcceptCharsetHeaderField",
        "Accept-Encoding" => "AcceptEncodingHeaderField",
        "Accept-language" => "AcceptLanguageHeaderField",
        "Authorization" => "AuthorizationHeaderField",
        "Content-Type" => "ContentTypeHeaderField",
        "Set-Cookie" => "SetCookieHeaderField"
    ];

    /**
     * Return the corresponding FQ classpath.
     * @param string $headerFieldName
     * @return string
     * @throws \Brickoo\Component\Http\Header\Aggregator\Exception\HeaderFieldClassNotFoundException
     */
    public function getClass($headerFieldName) {
        Assert::isString($headerFieldName);
        if (!$this->hasClass($headerFieldName)) {
            throw new HeaderFieldClassNotFoundException($headerFieldName);
        }
        return "Brickoo\\Component\\Http\\Header\\".$this->map[$headerFieldName];
    }

    /**
     * Check if the header field has a mapping class.
     * @param string $headerFieldName
     * @return boolean
     */
    public function hasClass($headerFieldName) {
        Assert::isStringOrInteger($headerFieldName);
        return isset($this->map[$headerFieldName]);
    }

}
