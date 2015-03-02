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

namespace Brickoo\Component\Http;

use Brickoo\Component\Http\Exception\StatusCodeUnknownException;
use Brickoo\Component\Common\Assert;

/**
 * HttpStatusCode
 *
 * Implements the http status codes and phrases.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpStatusCode {

    const CODE_CONTINUE = 100;
    const CODE_SWITCHING_PROTOCOLS = 101;
    const CODE_OK = 200;
    const CODE_CREATED = 201;
    const CODE_ACCEPTED = 202;
    const CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    const CODE_NO_CONTENT = 204;
    const CODE_RESET_CONTENT = 205;
    const CODE_PARTIAL_CONTENT = 206;
    const CODE_MULTIPLE_CHOICES = 300;
    const CODE_MOVED_PERMANENTLY = 301;
    const CODE_FOUND = 302;
    const CODE_SEE_OTHER = 303;
    const CODE_NOT_MODIFIED = 304;
    const CODE_USE_PROXY = 305;
    const CODE_TEMPORARY_REDIRECT = 307;
    const CODE_PERMANENT_REDIRECT = 308;
    const CODE_BAD_REQUEST = 400;
    const CODE_UNAUTHORIZED = 401;
    const CODE_PAYMENT_REQUIRED = 402;
    const CODE_FORBIDDEN = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_METHOD_NOT_ALLOWED = 405;
    const CODE_NOT_ACCEPTABLE = 406;
    const CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    const CODE_REQUEST_TIME_OUT = 408;
    const CODE_CONFLICT = 409;
    const CODE_GONE = 410;
    const CODE_LENGTH_REQUIRED = 411;
    const CODE_PRECONDITION_FAILED = 412;
    const CODE_REQUEST_ENTITY_TOO_LARGE = 413;
    const CODE_REQUEST_URI_TOO_LARGE = 414;
    const CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const CODE_EXPECTATION_FAILED = 417;
    const CODE_INTERNAL_SERVER_ERROR = 500;
    const CODE_NOT_IMPLEMENTED = 501;
    const CODE_BAD_GATEWAY = 502;
    const CODE_SERVICE_UNAVAILABLE = 503;
    const CODE_GATEWAY_TIME_OUT = 504;
    const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;

            /**
     * Holds the corresponding status code phrases.
     * 1xx: Informational - Request received, continuing process
     * 2xx: Success - The action was successfully received, understood, and accepted
     * 3xx: Redirection - Further action must be taken in order to complete the request
     * 4xx: Client Error - The request contains bad syntax or cannot be fulfilled
     * 5xx: Server Error - The server failed to fulfill an apparently valid request
     * @link http://tools.ietf.org/html/rfc2616#page-40
     * @var array
     */
    protected $statusPhrases = [
        self::CODE_CONTINUE => "Continue",
        self::CODE_SWITCHING_PROTOCOLS => "Switching Protocols",
        self::CODE_OK => "OK",
        self::CODE_CREATED => "Created",
        self::CODE_ACCEPTED => "Accepted",
        self::CODE_NON_AUTHORITATIVE_INFORMATION => "Non-Authoritative Information",
        self::CODE_NO_CONTENT => "No Content",
        self::CODE_RESET_CONTENT => "Reset Content",
        self::CODE_PARTIAL_CONTENT => "Partial Content",
        self::CODE_MULTIPLE_CHOICES => "Multiple Choices",
        self::CODE_MOVED_PERMANENTLY => "Moved Permanently",
        self::CODE_FOUND => "Found",
        self::CODE_SEE_OTHER => "See Other",
        self::CODE_NOT_MODIFIED => "Not Modified",
        self::CODE_USE_PROXY => "Use Proxy",
        self::CODE_TEMPORARY_REDIRECT => "Temporary Redirect",
        self::CODE_PERMANENT_REDIRECT => "Permanent Redirect",
        self::CODE_BAD_REQUEST => "Bad Request",
        self::CODE_UNAUTHORIZED => "Unauthorized",
        self::CODE_PAYMENT_REQUIRED => "Payment Required",
        self::CODE_FORBIDDEN => "Forbidden",
        self::CODE_NOT_FOUND => "Not Found",
        self::CODE_METHOD_NOT_ALLOWED => "Method Not Allowed",
        self::CODE_NOT_ACCEPTABLE => "Not Acceptable",
        self::CODE_PROXY_AUTHENTICATION_REQUIRED => "Proxy Authentication Required",
        self::CODE_REQUEST_TIME_OUT => "Request Time-out",
        self::CODE_CONFLICT => "Conflict",
        self::CODE_GONE => "Gone",
        self::CODE_LENGTH_REQUIRED => "Length Required",
        self::CODE_PRECONDITION_FAILED => "Precondition Failed",
        self::CODE_REQUEST_ENTITY_TOO_LARGE => "Request Entity Too Large",
        self::CODE_REQUEST_URI_TOO_LARGE => "Request-URI Too Large",
        self::CODE_UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
        self::CODE_REQUESTED_RANGE_NOT_SATISFIABLE => "Requested range not satisfiable",
        self::CODE_EXPECTATION_FAILED => "Expectation Failed",
        self::CODE_INTERNAL_SERVER_ERROR => "Internal Server Error",
        self::CODE_NOT_IMPLEMENTED => "Not Implemented",
        self::CODE_BAD_GATEWAY => "Bad Gateway",
        self::CODE_SERVICE_UNAVAILABLE => "Service Unavailable",
        self::CODE_GATEWAY_TIME_OUT => "Gateway Time-out",
        self::CODE_HTTP_VERSION_NOT_SUPPORTED => "HTTP Version not supported"
    ];

    /**
     * Returns the phrase for the status code.
     * @param integer $statusCode
     * @throws \Brickoo\Component\Http\Exception\StatusCodeUnknownException
     * @return string the status code phrase
     */
    public function getPhrase($statusCode) {
        Assert::isInteger($statusCode);

        if (!$this->hasPhrase($statusCode)) {
            throw new StatusCodeUnknownException($statusCode);
        }
        return $this->statusPhrases[$statusCode];
    }

    /**
     * Checks if the status code has a phrase.
     * @param integer $statusCode
     * @return boolean check result
     */
    public function hasPhrase($statusCode) {
        return array_key_exists($statusCode, $this->statusPhrases);
    }

}
