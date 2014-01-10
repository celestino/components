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

namespace Brickoo\Http\Resolver;

use Brickoo\Http\Header\GenericHeader,
    Brickoo\Http\Resolver\HeaderResolverPlugin,
    Brickoo\Http\Resolver\Exception\FileDoesNotExistException,
    Brickoo\Http\Resolver\Exception\FileIsNotReadableException,
    Brickoo\Http\Resolver\Exception\HeaderClassNotFoundException,
    Brickoo\Http\Resolver\Exception\MappingHeaderNotFoundException,
    Brickoo\Http\Resolver\Exception\WrongHeaderMapTypeException,
    Brickoo\Validation\Argument;

/**
 * HeaderResolver
 *
 * Implements a http header solver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HeaderResolver {

    /** @var string */
    private $headerMapFile;

    /** @var array */
    private $headerMap;

    /** @var \Brickoo\Http\Resolver\HeaderResolverPlugin */
    private $headersPlugin;

    /**
     * Class constructor.
     * @param string $headerMapFile a file containing a header file map
     * @param \Brickoo\Http\Resolver\HeaderResolverPlugin $headersPlugin
     * @throws \Brickoo\Http\Resolver\Exception\FileDoesNotExistException
     * @throws \Brickoo\Http\Resolver\Exception\FileIsNotReadableException
     * @return void
     */
    public function __construct($headerMapFile, HeaderResolverPlugin $headersPlugin) {
        Argument::IsString($headerMapFile);

        if (! file_exists($headerMapFile)) {
            throw new FileDoesNotExistException($headerMapFile);
        }

        if (! is_readable($headerMapFile)) {
            throw new FileIsNotReadableException($headerMapFile);
        }

        $this->headerMap = [];
        $this->headerMapFile = $headerMapFile;
        $this->headersPlugin = $headersPlugin;
    }

    /**
     * Returns a collection of http headers.
     * @return array containing \Brickoo\Http\Header values
     */
    public function getHeaders() {
        $pluginHeaders = $this->normalizeHeaders($this->headersPlugin->getHeaders());

        if (empty($this->headerMap)) {
            $this->loadHeaderMap();
        }

        $headers = [];
        foreach ($pluginHeaders as $headerName => $headerValue) {
            $headers[] = $this->getHeader($headerName, $headerValue);
        }
        return $headers;
    }

    /**
     * Loads the header map into memory.
     * @throws \Brickoo\Http\Resolver\Exception\WrongHeaderMapTypeException
     * @return \Brickoo\Http\Resolver\RequestHeaderResolver
     */
    private function loadHeaderMap() {
        $headerMap = include $this->headerMapFile;
        if (! is_array($headerMap)) {
            throw new WrongHeaderMapTypeException($headerMap);
        }
        $this->headerMap = $headerMap;
        return  $this;
    }

    /**
     * Returns the corresponding header instance.
     * @param string $headerName
     * @param string $headerValue
     * @return \Brickoo\Http\HttpHeader
     */
    private function getHeader($headerName, $headerValue) {
        if ($this->hasMappingHeaderClass($headerName)) {
            return $this->createMappingHeader($this->getMappingHeaderClass($headerName), $headerValue);
        }
        return $this->createGenericHeader($headerName, $headerValue);
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
     * Returns the header mapping class name.
     * @param string $headerName
     * @throws \Brickoo\Http\Resolver\Exception\MappingHeaderNotFoundException
     * @return string the mapping header class
     */
    private function getMappingHeaderClass($headerName) {
        if (! isset($this->headerMap[$headerName])) {
            throw new MappingHeaderNotFoundException($headerName);
        }
        return $this->headerMap[$headerName];
    }

    /**
     * Creates a header instance from a mapping class.
     * @param string $headerClass
     * @param string $headerValue
     * @throws \Brickoo\Http\Resolver\Exception\HeaderClassNotFoundException
     * @return \Brickoo\Http\Resolver\headerClass
     */
    private function createMappingHeader($headerClass, $headerValue) {
        if (! class_exists($headerClass)) {
            throw new HeaderClassNotFoundException($headerClass);
        }

        return new $headerClass($headerValue);
    }

    /**
     * Creates a generic header with the header name and value.
     * @param string $headerName
     * @param string $headerValue
     * @return \Brickoo\Http\Header\GenericHeader
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