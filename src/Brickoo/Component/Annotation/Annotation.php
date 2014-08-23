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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\Validation\Argument;

/**
 * Annotation
 *
 * Implements an annotation containing the target, name and values.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Annotation  {

    /** @const annotation targets */
    const TARGET_CLASS = 1;
    const TARGET_METHOD = 2;
    const TARGET_PROPERTY = 4;

    /** @var integer */
    protected $target;

    /** @var string */
    protected $targetLocation;

    /** @var string */
    protected $name;

    /** @var array */
    protected $values;

    /**
     * Class constructor.
     * @param integer $target
     * @param string $targetLocation
     * @param string $name the annotation name
     * @param array $values the annotation values
     */
    public function __construct($target, $targetLocation, $name, array $values = []) {
        Argument::isInteger($target);
        Argument::isString($targetLocation);
        Argument::isString($name);
        $this->target = $target;
        $this->targetLocation = $targetLocation;
        $this->name = $name;
        $this->values = $values;
    }

    /**
     * Returns the annotation target
     * @return integer the annotation target
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Returns the target location.
     * @return string the target location
     */
    public function getTargetLocation() {
        return $this->targetLocation;
    }

    /**
     * Returns the annotation name.
     * @return string annotation name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the annotation values.
     * @return array annotations values
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * Checks if the annotation has a value.
     * @return boolean check result
     */
    public function hasValues() {
        return (! empty($this->values));
    }

}
