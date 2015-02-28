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

namespace Brickoo\Component\Http\Aggregator;

use Brickoo\Component\Http\HttpMessageHeader;

/**
 * HttpRequestUriAggregator
 *
 * Implements a resolver for a http request uri.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpRequestUriAggregator implements UriAggregator {

    /** @var \Brickoo\Component\Http\HttpMessageHeader */
    private $header;

    /** @var array */
    private $serverValues;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Http\HttpMessageHeader $header
     * @param array $serverValues the server variables as key/value pairs
     */
    public function __construct(HttpMessageHeader $header, array $serverValues = []) {
        $this->header = $header;
        $this->serverValues = $serverValues;
    }

    /** {@inheritDoc} */
    public function getScheme() {
        if (! ($isSecure = $this->isForwardedFromHttps())) {
            $isSecure = $this->isHttpsMode();
        }
        return "http".($isSecure ? "s" : "");
    }

    /** {@inheritDoc} */
    public function getHostname() {
        if ($this->header->contains("Host")) {
            return $this->header->getField("Host")->getValue();
        }
        return $this->getServerVar("SERVER_NAME", $this->getServerVar("SERVER_ADDR", "localhost"));
    }

    /** {@inheritDoc} */
    public function getPort() {
        if ($this->header->contains("X-Forwarded-Port")) {
            return (int)$this->header->getField("X-Forwarded-Port")->getValue();
        }
        return (int)$this->getServerVar("SERVER_PORT", 80);
    }

    /** {@inheritDoc} */
    public function getPath() {
        if ((! $requestPath = $this->getServerVar("REQUEST_URI"))
            && (! $requestPath = $this->getServerVar("ORIG_PATH_INFO"))) {
            $requestPath = $this->getIisRequestUri();
        }
        return "/".trim(rawurldecode(strval(parse_url($requestPath, PHP_URL_PATH))), "/");
    }

    /** {@inheritDoc} */
    public function getQueryString() {
        if (! $queryString = $this->getServerVar("QUERY_STRING")) {
            $queryArray = [];
            foreach ($_GET as $key => $value) {
                $queryArray[] = $key."=".$value;
            }
            $queryString = implode("&", $queryArray);
        }

        return urldecode($queryString);
    }

    /** {@inheritDoc} */
    public function getFragment() {
        return "";
    }

    /**
     * Check if the request was forwarded from a https connection.
     * @return boolean check result
     */
    private function isForwardedFromHttps() {
        $isSecure = false;
        if ($this->header->contains("X-Forwarded-Proto")) {
            $httpsForwarded = $this->header->getField("X-Forwarded-Proto")->getValue();
            $isSecure = (strtolower($httpsForwarded) == "https");
        }
        return $isSecure;
    }

    /**
     * Check if the server provides a https connection.
     * @return boolean check result
     */
    private function isHttpsMode() {
        $isSecure = false;
        if ($httpsMode = $this->getServerVar("HTTPS", "off")) {
            $isSecure = in_array(strtolower($httpsMode), ["on", "1"]);
        }
        return $isSecure;
    }

    /**
     * Return the IIS request ur assigned if available.
     * @return string|null the request uri or null on unavailable
     */
    private function getIisRequestUri() {
        if ($this->header->contains("X-Original-Url")) {
            return $this->header->getField("X-Original-Url")->getValue();
        }

        if ($this->header->contains("X-Rewrite-Url")) {
            return $this->header->getField("X-Rewrite-Url")->getValue();
        }
        return null;
    }

    /**
     * Return a server variable or the default value if it does not exist.
     * @param string $key the key of the server variable
     * @param string|null $defaultValue the default value to return
     * @return string|null the value of the server variable otherwise the default value
     */
    private function getServerVar($key, $defaultValue = null) {
        if (isset($this->serverValues[$key])) {
            return $this->serverValues[$key];
        }
        return $defaultValue;
    }

}
