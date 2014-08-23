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

use Brickoo\Component\Routing\Route\Exception\ParameterNotAvailableException;
use Brickoo\Component\Validation\Argument;

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
        Argument::isString($parameter);

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
        Argument::isString($parameter);
        return isset($this->parameters[$parameter]);
    }

}
