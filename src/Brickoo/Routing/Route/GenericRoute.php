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

namespace Brickoo\Routing\Route;

use Brickoo\Routing\Route,
    Brickoo\Validation\Argument;

/**
 * GenericRoute
 *
 * Implents a generic route.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class GenericRoute implements Route {

    /** @var string */
    protected $name;

    /** @var string */
    protected $path;

    /** @var string */
    protected $controller;

    /** @var string */
    protected $action;

    /** @var array */
    protected $defaultValues;

    /** @var array */
    protected $rules;

    /**
     * Class constructor.
     * @param string $name
     * @param string $path
     * @param string $controller
     * @param string $action
     * @param array $rules
     * @param array $defaultValues
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct($name, $path, $controller, $action) {
        Argument::IsString($name);
        Argument::IsString($path);
        Argument::IsString($controller);
        Argument::IsString($action);

        $this->name = $name;
        $this->path = $path;
        $this->controller = $controller;
        $this->action = $action;
        $this->rules = [];
        $this->defaultValues = [];
    }

    /** {@inheritDoc} */
    public function getName() {
        return $this->name;
    }

    /** {@inheritDoc} */
    public function getPath() {
        return $this->path;
    }

    /** {@inheritDoc} */
    public function getController() {
        return $this->controller;
    }

    /** {@inheritDoc} */
    public function getAction() {
        return $this->action;
    }

    /** {@inheritDoc} */
    public function getRules() {
        return $this->rules;
    }

    /** {@inheritDoc} */
    public function getRule($parameter) {
        Argument::IsString($parameter);

        if (! $this->hasRule($parameter)) {
            throw new \UnexpectedValueException(
                sprintf("The rule for `%s` does not exist.", $parameter)
            );
        }

        return $this->rules[$parameter];
    }

    /** {@inheritDoc} */
    public function hasRules() {
        return (! empty($this->rules));
    }

    /** {@inheritDoc} */
    public function hasRule($parameter) {
        Argument::IsString($parameter);
        return array_key_exists($parameter, $this->rules);
    }

    /** {@inheritDoc} */
    public function setRules(array $rules) {
        $this->rules = $rules;
        return $this;
    }

    /** {@inheritDoc} */
    public function getDefaultValues() {
        return $this->defaultValues;
    }

    /** {@inheritDoc} */
    public function getDefaultValue($parameter) {
        Argument::IsString($parameter);

        if (!$this->hasDefaultValue($parameter)) {
            throw new \UnexpectedValueException(
                sprintf("The default vaule for the parameter `%s` does not exist.", $parameter)
            );
        }
        return $this->defaultValues[$parameter];
    }

    /** {@inheritDoc} */
    public function hasDefaultValue($parameter) {
        Argument::IsString($parameter);
        return array_key_exists($parameter, $this->defaultValues);
    }

    /** {@inheritDoc} */
    public function setDefaultValues(array $defaultValues) {
        $this->defaultValues = $defaultValues;
        return $this;
    }

}