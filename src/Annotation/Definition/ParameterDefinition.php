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

namespace Brickoo\Component\Annotation\Definition;

use Brickoo\Component\Validation\Argument;

/**
 * ParameterDefinition
 *
 * Implements a parameter definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ParameterDefinition {

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var boolean */
    private $required;

    /**
     * Class constructor.
     * @param string $name
     * @param string $type
     * @param boolean $required
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $type, $required = true) {
        Argument::isString($name);
        Argument::isString($type);
        Argument::isBoolean($required);
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
    }

    /**
     * Returns the parameter name.
     * @return string the parameter name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the parameter type.
     * @return string the parameter type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Checks if the parameter is required.
     * @return boolean check result
     */
    public function isRequired() {
        return $this->required;
    }

}
