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

namespace Brickoo\Component\Routing\Route;

use Brickoo\Component\Routing\Router;
use Brickoo\Component\Routing\Route\Exception\PathNotValidException;
use Brickoo\Component\Routing\Route\Exception\RouteRequiredParametersMissingException;
use Brickoo\Component\Common\Assert;

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
        Assert::isString($baseUrl);
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
        Assert::isString($routeName);
        Assert::isString($queryString);

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
        return rtrim($this->baseUrl, "/").$uriPath.(empty($queryString) ? "" : "?".ltrim($queryString, "?"));
    }

}
