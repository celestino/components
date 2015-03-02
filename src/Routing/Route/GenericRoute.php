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

use Brickoo\Component\Common\Assert;
use UnexpectedValueException;

/**
 * GenericRoute
 *
 * Implements a generic route.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class GenericRoute implements Route {

    /** @var string */
    protected $name;

    /** @var string */
    protected $path;

    /** @var string */
    protected $controller;

    /** @var array */
    protected $defaultValues;

    /** @var array */
    protected $rules;

    /**
     * Class constructor.
     * @param string $name
     * @param string $path
     * @param string $controller
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $path, $controller) {
        Assert::isString($name);
        Assert::isString($path);
        Assert::isString($controller);

        $this->name = $name;
        $this->path = $path;
        $this->controller = $controller;
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
    public function getRules() {
        return $this->rules;
    }

    /** {@inheritDoc} */
    public function getRule($parameter) {
        Assert::isString($parameter);

        if (!$this->hasRule($parameter)) {
            throw new UnexpectedValueException(
                sprintf("The rule for `%s` does not exist.", $parameter)
            );
        }

        return $this->rules[$parameter];
    }

    /** {@inheritDoc} */
    public function hasRules() {
        return (!empty($this->rules));
    }

    /** {@inheritDoc} */
    public function hasRule($parameter) {
        Assert::isString($parameter);
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
        Assert::isString($parameter);

        if (!$this->hasDefaultValue($parameter)) {
            throw new UnexpectedValueException(
                sprintf("The default value for the parameter `%s` does not exist.", $parameter)
            );
        }
        return $this->defaultValues[$parameter];
    }

    /** {@inheritDoc} */
    public function hasDefaultValue($parameter) {
        Assert::isString($parameter);
        return array_key_exists($parameter, $this->defaultValues);
    }

    /** {@inheritDoc} */
    public function setDefaultValues(array $defaultValues) {
        $this->defaultValues = $defaultValues;
        return $this;
    }

}
