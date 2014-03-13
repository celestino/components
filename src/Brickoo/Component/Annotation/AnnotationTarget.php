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

use Brickoo\Component\Annotation\AnnotationTargetTypes,
    Brickoo\Component\Validation\Argument;

/**
 * AnnotationTarget
 *
 * Implements an annotation target.
 * Used as definition container for an annotation.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationTarget implements AnnotationTargetTypes {

    /** @var integer */
    private $targetType;

    /** @var string */
    private $className;

    /** @var string */
    private $targetName;

    /**
     * Class constructor.
     * @param integer $targetType
     * @param string $className
     * @param string $targetName
     * @throws \InvalidArgumentException
     */
    public function __construct($targetType, $className, $targetName = "") {
        Argument::IsInteger($targetType);
        Argument::IsString($className);
        Argument::IsString($targetName);
        $this->targetType = $targetType;
        $this->className = $className;
        $this->targetName = $targetName;
    }

    /**
     * Returns the annotation target type.
     * @return integer the target type
     */
    public function getType() {
        return $this->targetType;
    }

    /**
     * Checks if the target matches a type.
     * @param integer $targetType
     * @trows \InvalidArgumentException
     * @return boolean check result
     */
    public function isTypeOf($targetType) {
        Argument::IsInteger($targetType);
        return $targetType === $this->getType();
    }

    /**
     * Returns the target class name.
     * @return string the class name
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Returns the target name if available.
     * @return string method|property name
     */
    public function getName() {
        return $this->targetName;
    }

}