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

use Brickoo\Component\Common\Assert;

/**
 * Uri
 *
 * Implements an uniform resource identifier.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Uri {

    /** @var string */
    private $scheme;

    /** @var \Brickoo\Component\Http\UriAuthority */
    private $authority;

    /** @var string */
    private $path;

    /** @var \Brickoo\Component\Http\UriQuery */
    private $query;

    /** @var string */
    private $fragment;

    /**
     * Class constructor.
     * @param string $scheme the uri protocol scheme
     * @param \Brickoo\Component\Http\UriAuthority $authority
     * @param string $path the uri path
     * @param \Brickoo\Component\Http\UriQuery $query
     * @param string $fragment
     */
    public function __construct($scheme, UriAuthority $authority, $path, UriQuery $query, $fragment) {
        Assert::isString($scheme);
        Assert::isString($path);
        Assert::isString($fragment);

        $this->scheme = $scheme;
        $this->authority = $authority;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * Returns the request uri protocol scheme.
     * @return string the uri scheme
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * Returns the uri authority component.
     * @return \Brickoo\Component\Http\UriAuthority
     */
    public function getAuthority() {
        return $this->authority;
    }

    /**
     * Returns the uri hostname.
     * @return string the hostname
     */
    public function getHostname() {
        return $this->authority->getHostname();
    }

    /**
     * Returns the uri path.
     * @return string the uri path
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns the uri query component.
     * @return \Brickoo\Component\Http\UriQuery
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Returns the uri fragment.
     * @return string the uri fragment
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * Returns the string representation of the uri.
     * @return string the uri representation
     */
    public function toString() {
        $uriParts = sprintf("%s://%s", $this->getScheme(), $this->getAuthority()->toString());

        if ($queryString = $this->getQuery()->toString()) {
            $queryString = "?".$queryString;
        }

        if ($fragment = $this->getFragment()) {
            $fragment = "#".$fragment;
        }

        return  $uriParts.$this->getPath().$queryString.$fragment;
    }

 }
