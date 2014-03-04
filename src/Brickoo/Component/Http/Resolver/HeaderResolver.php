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

use Brickoo\Component\Autoloader\Exception\FileDoesNotExistException,
    Brickoo\Component\Http\Header\GenericHeader,
    Brickoo\Component\Http\Resolver\HeaderResolverPlugin,
    Brickoo\Component\Http\Resolver\Exception\HeaderClassNotFoundException;

/**
 * HeaderResolver
 *
 * Implements a http header solver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HeaderResolver {

    /** @var array */
    private $headerMap;

    /** @var \Brickoo\Component\Http\Resolver\HeaderResolverPlugin */
    private $resolverPlugin;

    /** @var array */
    private $loadedHeaders;

    /**
     * Class constructor.
     * @param array $headerMap a map array containing the header nam as key and target class as value
     * @param \Brickoo\Component\Http\Resolver\HeaderResolverPlugin $resolverPlugin
     * @return void
     */
    public function __construct(array $headerMap, HeaderResolverPlugin $resolverPlugin) {
        $this->headerMap = $headerMap;
        $this->resolverPlugin = $resolverPlugin;
        $this->loadedHeaders = [];
    }

    /**
     * Returns a collection of http headers.
     * @return array containing \Brickoo\Component\Http\HttpHeader values
     */
    public function getHeaders() {
        $this->loadHeaders();

        $headers = [];
        foreach ($this->loadedHeaders as $headerName => $headerValue) {
            $headers[] = $this->getHeader($headerName, $headerValue);
        }
        return $headers;
    }

    /**
     * Loads the headers into local cache.
     * @return \Brickoo\Component\Http\Resolver\HeaderResolver
     */
    private function loadHeaders() {
        if (empty($this->loadedHeaders)) {
            $this->loadedHeaders = $this->normalizeHeaders($this->resolverPlugin->getHeaders());
        }
        return $this;
    }

    /**
     * Checks if the header has a mapping class.
     * @param string $headerName
     * @return boolean check result
     */
    private function hasMappingHeaderClass($headerName) {
        return array_key_exists($headerName, $this->headerMap);
    }

    /**
     * Returns the corresponding header instance.
     * @param string $headerName
     * @param string $headerValue
     * @return \Brickoo\Component\Http\HttpHeader
     */
    private function getHeader($headerName, $headerValue) {
        if ($this->hasMappingHeaderClass($headerName)) {
            return $this->createMappingHeader($this->headerMap[$headerName], $headerValue);
        }
        return $this->createGenericHeader($headerName, $headerValue);
    }

    /**
     * Creates a header instance from a mapping class.
     * @param string $headerClass
     * @param string $headerValue
     * @throws \Brickoo\Component\Http\Resolver\Exception\HeaderClassNotFoundException
     * @return \Brickoo\Component\Http\Resolver\headerClass
     */
    private function createMappingHeader($headerClass, $headerValue) {
        try {
            class_exists($headerClass);
        }
        catch (FileDoesNotExistException $exception) {
            throw new HeaderClassNotFoundException($headerClass, $exception);
        }
        return new $headerClass($headerValue);
    }

    /**
     * Creates a generic header with the header name and value.
     * @param string $headerName
     * @param string $headerValue
     * @return \Brickoo\Component\Http\Header\GenericHeader
     */
    private function createGenericHeader($headerName, $headerValue) {
        return new GenericHeader($headerName, $headerValue);
    }

    /**
     * Normalizes the headers keys.
     * @param array $headers the headers to normalize
     * @return array the normalized headers
     */
    private function normalizeHeaders(array $headers) {
        $normalizedHeaders = [];

        foreach ($headers as $headerName => $headerValue) {
            $headerName = str_replace(" ", "-", ucwords(
                strtolower(str_replace(array("_", "-"), " ", $headerName))
            ));
            $normalizedHeaders[$headerName] = $headerValue;
        }

        ksort($normalizedHeaders);
        return $normalizedHeaders;
    }

}