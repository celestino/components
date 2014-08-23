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

namespace Brickoo\Component\Routing\Route;

use Brickoo\Component\Routing\Router;
use Brickoo\Component\Routing\Route\Exception\PathNotValidException;
use Brickoo\Component\Routing\Route\Exception\RouteRequiredParametersMissingException;
use Brickoo\Component\Validation\Argument;

/**
 * RouteUriBuilder
 *
 * Implements an uri builder to create a route matching uri.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RouteUriBuilder {

    /** @var string */
    private $baseUrl;

    /** @var \Brickoo\Component\Routing\Router */
    private $router;

    /** @var \Brickoo\Component\Routing\Route\RoutePathRegexGenerator */
    private $regexGenerator;

    /**
     * Class constructor.
     * @param string $baseUrl the base url e.g. http://localhost:8080
     * @param \Brickoo\Component\Routing\Router $router
     * @param \Brickoo\Component\Routing\Route\RoutePathRegexGenerator $regexGenerator
     */
    public function __construct($baseUrl, Router $router, RoutePathRegexGenerator $regexGenerator) {
        Argument::isString($baseUrl);
        $this->baseUrl = $baseUrl;
        $this->router = $router;
        $this->regexGenerator = $regexGenerator;
    }

    /**
     * Builds an uri string based on the parameters provided.
     * @param string $routeName the route to use for the build
     * @param array $pathParameters the path parameters as key/value pairs
     * @param string $queryString
     * @throws \Brickoo\Component\Routing\Route\Exception\PathNotValidException
     * @internal param string $queryParameters the query parameters
     * @return string the built uri
     */
    public function build($routeName, array $pathParameters = [], $queryString = "") {
        Argument::isString($routeName);
        Argument::isString($queryString);

        $route = $this->router->getRoute($routeName);
        $expectedPath = $this->getExpectedRoutePath($route, $pathParameters);

        $matches = [];
        if (preg_match_all($this->regexGenerator->generate($route), $expectedPath, $matches) === 0) {
            throw new PathNotValidException($routeName, $expectedPath);
        }

        return $this->createUriString($expectedPath, $queryString);
    }

    /**
     * Returns the expected uri path to validate against the route path.
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @param array $pathParameters the path parameters to use
     * @throws \Brickoo\Component\Routing\Route\Exception\RouteRequiredParametersMissingException
     * @return string the uri path expected
     */
    private function getExpectedRoutePath(Route $route, $pathParameters) {
        $routePath = $route->getPath();
        $pathParameters = array_merge($route->getDefaultValues(), $pathParameters);

        foreach ($pathParameters as $parameter => $value) {
            $routePath = str_replace("{".$parameter."}", $value, $routePath);
        }

        $matches = [];
        if (preg_match_all("~(\\{(?<missingParameters>[\\w]+)\\})~", $routePath, $matches) > 0) {
            throw new RouteRequiredParametersMissingException($route->getName(), $matches["missingParameters"]);
        }

        return $routePath;
    }

    /**
     * Returns the created uri string.
     * @param string $uriPath the uri path
     * @param string $queryString the query string
     * @return string the created uri string
     */
    private function createUriString($uriPath, $queryString) {
        return rtrim($this->baseUrl, "/").$uriPath. (empty($queryString) ? "" : "?".ltrim($queryString, "?"));
    }

}
