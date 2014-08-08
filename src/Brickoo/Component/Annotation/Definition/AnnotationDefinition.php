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
        Argument::IsString($annotationName);
        Argument::IsInteger($target);
        Argument::IsBoolean($required);
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
        Argument::IsInteger($target);
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
