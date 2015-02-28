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

use Brickoo\Component\Http\HttpHeaderField;
use Brickoo\Component\Http\Header\Exception\InvalidCookieValueException;
use Brickoo\Component\Validation\Argument;

/**
 * SetCookieHeaderField
 *
 * Implements a http cookie header field.
 * @link http://tools.ietf.org/html/rfc6265
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SetCookieHeaderField implements HttpHeaderField {

    use CommonHeaderFieldStructure;

    /** @var string */
    private $cookieName;

    /** @var string */
    private $cookieValue;

    /** @var string|null */
    private $expires;

    /** @var integer|null */
    private $maxAge;

    /** @var string|null */
    private $domain;

    /** @var string|null */
    private $path;

    /** @var boolean */
    private $secure;

    /** @var boolean */
    private $httpOnly;

    /**
     * Class constructor.
     * @param string $cookieName
     * @param string $cookieValue
     */
    public function __construct($cookieName, $cookieValue = "") {
        Argument::isString($cookieName);
        $this->cookieName = $cookieName;
        $this->setName("Set-Cookie");
        $this->setCookieValue($cookieValue);
        $this->secure = false;
        $this->httpOnly = false;
    }

    /**
     * Set the cookie value.
     * @param string $cookieValue
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Http\Header\Exception\InvalidCookieValueException
     * @return \Brickoo\Component\Http\Header\SetCookieHeaderField
     */
    public function setCookieValue($cookieValue) {
        Argument::isString($cookieValue);
        if (preg_match("~[,;\\s]+~", $cookieValue) == 1) {
            throw new InvalidCookieValueException($cookieValue);
        }
        $this->cookieValue = $cookieValue;
        return $this;
    }

    /**
     * Set the expiration date.
     * @param string $expirationDate
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\Header\SetCookieHeaderField
     */
    public function setExpirationDate($expirationDate) {
        Argument::isString($expirationDate);
        $expirationDate = preg_match("~^[0-9]{10}$~", $expirationDate) ?
            intval($expirationDate) : strtotime($expirationDate);
        $this->expires = date(DATE_RFC1123, $expirationDate);
        return $this;
    }

    /**
     * Set the maximum age of the cookie storage.
     * @param integer $maxAge
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\Header\SetCookieHeaderField
     */
    public function setMaxAge($maxAge) {
        Argument::isInteger($maxAge);
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * Restrict to domain.
     * @param string $domain
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\Header\SetCookieHeaderField
     */
    public function setDomain($domain) {
        Argument::isString($domain);
        $this->domain = $domain;
        return $this;
    }

    /**
     * Enable or disable tls only.
     * @param boolean $secure
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\Header\SetCookieHeaderField
     */
    public function setSecure($secure) {
        Argument::isBoolean($secure);
        $this->secure = $secure;
        return $this;
    }

    /**
     * Enable or disable http only.
     * @param boolean $httpOnly
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\Header\SetCookieHeaderField
     */
    public function setHttpOnly($httpOnly) {
        Argument::isBoolean($httpOnly);
        $this->httpOnly = $httpOnly;
        return $this;
    }

    /** {@inheritdoc} */
    public function getValue() {
        $this->setValue(sprintf("%s=%s%s", $this->cookieName, $this->cookieValue, $this->getAttributesRepresentation()));
        return $this->headerFieldValue;
    }

    /**
     * Return the cookie attributes representation
     * @return string the cookie attributes representation
     */
    private function getAttributesRepresentation() {
        $attributes = $this->getAttributesSet();
        $representation = call_user_func_array(
            "sprintf",
            array_merge([implode("; ", array_keys($attributes))], array_values($attributes))
        );
        return empty($representation) ? "" : "; ".$representation;
    }

    /**
     * Return the attributes set.
     * @return array the attributes set
     */
    private function getAttributesSet() {
        return array_filter(
            [
                "Expires=%s" => $this->expires,
                "Max-Age=%d" => $this->maxAge,
                "Domain=%s" => $this->domain,
                "Path=%s" => $this->path,
                "Secure" => $this->secure,
                "HttpOnly" => $this->httpOnly
            ],
            function($attribute) {
                return $attribute !== null || $attribute === true;
            }
        );
    }

}
