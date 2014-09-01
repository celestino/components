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

namespace Brickoo\Component\Http\Resolver;

use Brickoo\Component\Http\HttpMessageHeader;
use Brickoo\Component\Http\UriResolver;

/**
 * HttpRequestUriResolver
 *
 * Implements a resolver for a http request uri.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpRequestUriResolver implements UriResolver {

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
            return $this->header->getHeader("Host")->getValue();
        }
        return $this->getServerVar("SERVER_NAME", $this->getServerVar("SERVER_ADDR", "localhost"));
    }

    /** {@inheritDoc} */
    public function getPort() {
        if ($this->header->contains("X-Forwarded-Port")) {
            return (int)$this->header->getHeader("X-Forwarded-Port")->getValue();
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
            $httpsForwarded = $this->header->getHeader("X-Forwarded-Proto")->getValue();
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
            return $this->header->getHeader("X-Original-Url")->getValue();
        }

        if ($this->header->contains("X-Rewrite-Url")) {
            return $this->header->getHeader("X-Rewrite-Url")->getValue();
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
