<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

/**
 * RoutePathRegexGenerator
 *
 * Implementation of a route regular expression generator.
 * The path can be manipulated using the aliases to handle
 * expected segments as an OR condition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RoutePathRegexGenerator  {

    /** @var array */
    private $aliases;

    /**
     * Class constructor.
     * @param array $aliases the routing aliases
     */
    public function __construct(array $aliases = []) {
        $this->aliases = $aliases;
    }

    /**
     * Returns a regular expression from the route to match a request path.
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @return string the regular expression for the route
     */
    public function generate(Route $route) {
        $routePath  = $this->getRoutePath($route);

        $matches = [];
        if (preg_match_all("~(\\{(?<parameters>[\\w]+)\\})~", $routePath, $matches)) {
            $this->replaceRoutePathWithRulesExpressions($routePath, $matches['parameters'], $route);
        }

        return "~^/".trim($routePath, "/")."$~i";
    }

    /**
     * Returns the route path containing the aliases definitions if any given.
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @return string the modified route path containing the aliases
     */
    private function getRoutePath(Route $route) {
        $routePath = $route->getPath();

        foreach ($this->aliases as $routeKey => $routeAlias) {
            if (strpos($routePath, $routeKey) !== false) {
                $replacement = sprintf("(%s|%s)", $routeKey, preg_quote($routeAlias, "~"));
                $routePath = str_replace($routeKey, $replacement, $routePath);
                break;
            }
        }
        return $routePath;
    }

    /**
     * Replaces the route parameters with the rules defined.
     * @param string $routePath the route path
     * @param string[] $parameters the dynamic parameters of the route
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @return void
     */
    private function replaceRoutePathWithRulesExpressions(&$routePath, array $parameters, Route $route) {
        foreach ($parameters as $parameterName) {
            if ($route->hasRule($parameterName)) {
                $this->replaceRoutePathParameter($routePath, $parameterName, $route);
            }
        }
    }

    /**
     * Replace route path parameter placeholder.
     * @param string $routePath
     * @param string $parameterName
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @return void
     */
    private function replaceRoutePathParameter(&$routePath, $parameterName, Route $route) {
        if (strpos($routePath, "/{".$parameterName."}") !== false) {
            $routePath = str_replace("/{".$parameterName."}",
                ($route->hasDefaultValue($parameterName) ?
                    "(/(?<".$parameterName.">(".$route->getRule($parameterName).")?))?" :
                    "/(?<".$parameterName.">".$route->getRule($parameterName).")"
                ),
                $routePath
            );
        }
        else {
            $routePath = str_replace("{".$parameterName."}",
                ($route->hasDefaultValue($parameterName) ?
                    "(?<".$parameterName.">(".$route->getRule($parameterName).")?)" :
                    "(?<".$parameterName.">".$route->getRule($parameterName).")"
                ),
                $routePath
            );
        }
    }

}
