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

namespace Brickoo\Component\Routing\Route\Matcher;

use Brickoo\Component\Http\HttpRequest;
use Brickoo\Component\Routing\Route\HttpRoute;
use Brickoo\Component\Routing\Route\Route;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Routing\Route\RoutePathRegexGenerator;

/**
 * CommonRouteMatcherRoutines
 *
 * Implementation common http route matcher routines.
 * Following DRY principle.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
trait CommonRouteMatcherRoutines {

    /** @var array */
    private $pathParameters;

    /**
     * Checks if the route does match the request uri path.
     * On matching, the request matching parameters will be extracted.
     * @param string $matchingPath
     * @param string $routeRegexPath
     * @return boolean check result
     */
    private function isMatchingRoute($matchingPath, $routeRegexPath) {
        $this->pathParameters = array();
        return (preg_match($routeRegexPath, $matchingPath, $this->pathParameters) == 1);
    }

    /**
     * Returns the route matched parameters.
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @return array
     */
    private function collectRouteParameters(Route $route) {
        if (!$route->hasRules()) {
            return [];
        }

        $routeParameters = [];
        foreach (array_keys($route->getRules()) as $ruleParameter) {
            $routeParameters[$ruleParameter] = $this->getRuleCorrespondingRouteParameter($ruleParameter, $route);
        }
        return $routeParameters;
    }

    /**
     * Return the rule corresponding route parameter.
     * @param string $ruleParameter
     * @param Route $route
     * @return mixed
     */
    private function getRuleCorrespondingRouteParameter($ruleParameter, Route $route) {
        if (isset($this->pathParameters[$ruleParameter])
            && (!empty($this->pathParameters[$ruleParameter]))) {
                return $this->pathParameters[$ruleParameter];
        }
        return $route->getDefaultValue($ruleParameter);
    }

}
