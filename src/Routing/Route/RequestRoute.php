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

use Brickoo\Component\Routing\Route\Exception\ParameterNotAvailableException;
use Brickoo\Component\Common\Assert;

/**
 * RequestRoute
 *
 * Implementation of an executable route containing the responsible route.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class RequestRoute {

    /** @var \Brickoo\Component\Routing\Route\Route */
    private $route;

    /** @var array */
    private $parameters;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Routing\Route\Route $route the matching request route
     * @param array $parameters the parameters extracted from the request
     */
    public function __construct(Route $route, array $parameters = []) {
        $this->route = $route;
        $this->parameters = $parameters;
    }

    /**
     * Return the matched route.
     * @return \Brickoo\Component\Routing\Route\Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Return the route parameters and values as pairs.
     * @return array the route parameters and values
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Return the value of the parameter.
     * @param string $parameter the parameter name
     * @throws \Brickoo\Component\Routing\Route\Exception\ParameterNotAvailableException
     * @return string the parameter value
     */
    public function getParameter($parameter) {
        Assert::isString($parameter);

        if (! $this->hasParameter($parameter)) {
            throw new ParameterNotAvailableException($parameter);
        }

        return $this->parameters[$parameter];
    }

    /**
     * Check if the parameter is available.
     * @param string $parameter the parameter name
     * @return boolean check result
     */
    public function hasParameter($parameter) {
        Assert::isString($parameter);
        return isset($this->parameters[$parameter]);
    }

}
