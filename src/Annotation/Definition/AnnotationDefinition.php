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

namespace Brickoo\Component\Annotation\Definition;

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Validation\Argument;

/**
 * AnnotationDefinition
 *
 * Implements an annotation definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationDefinition {

    /** @var integer */
    private $target;

    /** @var string */
    private $annotationName;

    /** @var boolean */
    private $required;

    /** @var ParameterDefinition[] */
    private $requiredParameters;

    /** @var array<ParameterDefinition> */
    private $optionalParameters;

    /**
     * Class constructor.
     * @param string $annotationName
     * @param integer $target
     * @param boolean $required
     * @throws \InvalidArgumentException
     */
    public function __construct($annotationName, $target = Annotation::TARGET_CLASS, $required = true) {
        Argument::isString($annotationName);
        Argument::isInteger($target);
        Argument::isBoolean($required);
        $this->target = $target;
        $this->annotationName = $annotationName;
        $this->required = $required;
        $this->requiredParameters = [];
        $this->optionalParameters = [];
    }

    /**
     * Returns the target.
     * @return integer the annotation target
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Checks if the annotation matches a target.
     * @param integer $target
     * @return boolean check result
     */
    public function isTarget($target) {
        Argument::isInteger($target);
        return $this->getTarget() == $target;
    }

    /**
     * Returns the annotation name.
     * @return string the annotation name
     */
    public function getName() {
        return $this->annotationName;
    }

    /**
     * Checks if the annotation is required.
     * @return boolean check result
     */
    public function isRequired() {
        return $this->required;
    }

    /**
     * Adds a parameter to annotation.
     * @param \Brickoo\Component\Annotation\Definition\ParameterDefinition $parameter
     * @return \Brickoo\Component\Annotation\Definition\AnnotationDefinition
     */
    public function addParameter(ParameterDefinition $parameter) {
        if ($parameter->isRequired()) {
            $this->requiredParameters[] = $parameter;
        }
        else {
            $this->optionalParameters[] = $parameter;
        }
        return $this;
    }

    /**
     * Returns the required parameters.
     * @return ParameterDefinition[] the required parameters
     */
    public function getRequiredParameters() {
        return $this->requiredParameters;
    }

    /**
     * Checks if the annotation has required parameters.
     * @return boolean check result
     */
    public function hasRequiredParameters() {
        return empty($this->requiredParameters) === false;
    }

    /**
     * Returns the optional parameters.
     * @return ParameterDefinition[] the optional parameters
     */
    public function getOptionalParameters() {
        return $this->optionalParameters;
    }

}
