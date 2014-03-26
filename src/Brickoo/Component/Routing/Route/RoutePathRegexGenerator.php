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

        return "~^/". trim($routePath, "/") ."$~i";
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
     * @param array $parameters the dynamic parameters of the route
     * @param \Brickoo\Component\Routing\Route\Route $route
     * @internal param string $regex the regular expression to modify
     * @return void
     */
    private function replaceRoutePathWithRulesExpressions(&$routePath, array $parameters, Route $route) {
        foreach ($parameters as $parameterName) {
            if (! $route->hasRule($parameterName)) {
                continue;
            }

            if (strpos($routePath, "/{". $parameterName ."}") !== false) {
                $routePath = str_replace("/{". $parameterName ."}",
                    ($route->hasDefaultValue($parameterName) ?
                        "(/(?<". $parameterName .">(". $route->getRule($parameterName) .")?))?" :
                        "/(?<". $parameterName .">". $route->getRule($parameterName) .")"
                    ),
                    $routePath
                );
            }
            else {
                $routePath = str_replace("{". $parameterName ."}",
                    ($route->hasDefaultValue($parameterName) ?
                        "(?<". $parameterName .">(". $route->getRule($parameterName) .")?)" :
                        "(?<". $parameterName .">". $route->getRule($parameterName) .")"
                    ),
                    $routePath
                );
            }
        }
    }

}