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
use Brickoo\Component\Http\Header\Exception\InvalidCookieValueException;
use Brickoo\Component\Validation\Argument;

/**
 * SetCookieHeader
 *
 * Implements a http cookie header.
 * @link http://tools.ietf.org/html/rfc6265
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SetCookieHeader implements HttpHeader {

    use CommonHeaderStructure;

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
     * @return \Brickoo\Component\Http\Header\SetCookieHeader
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
     * @return \Brickoo\Component\Http\Header\SetCookieHeader
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
     * @return \Brickoo\Component\Http\Header\SetCookieHeader
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
     * @return \Brickoo\Component\Http\Header\SetCookieHeader
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
     * @return \Brickoo\Component\Http\Header\SetCookieHeader
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
     * @return \Brickoo\Component\Http\Header\SetCookieHeader
     */
    public function setHttpOnly($httpOnly) {
        Argument::isBoolean($httpOnly);
        $this->httpOnly = $httpOnly;
        return $this;
    }

    /** {@inheritdoc} */
    private function build() {
        $this->setValue(sprintf("%s=%s%s", $this->cookieName, $this->cookieValue, $this->getAttributesRepresentation()));
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
        return empty($representation) ? "": "; ".$representation;
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
