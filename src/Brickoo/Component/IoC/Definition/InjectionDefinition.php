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

namespace Brickoo\Component\IoC\Definition;

use Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer;
use Brickoo\Component\Validation\Argument;

/**
 * InjectionDefinition
 *
 * Implements a method injection definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InjectionDefinition {

    /** @const injection targets */
    const TARGET_CONSTRUCTOR = "constructor";
    const TARGET_METHOD = "method";
    const TARGET_PROPERTY = "property";

    /** @var string */
    private $target;

    /** @var string */
    private $targetName;

    /** @var \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer */
    private $argumentsContainer;

    /**
     * Class constructor.
     * @param string $target
     * @param string $targetName
     * @param \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer $container
     * @throws \InvalidArgumentException
     */
    public function __construct($target, $targetName, ArgumentDefinitionContainer $container) {
        Argument::IsString($target);
        Argument::IsString($targetName);
        $this->target = $target;
        $this->targetName = $targetName;
        $this->argumentsContainer = $container;
    }

    /**
     * Returns the injection target.
     * @return string the target
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Checks if the injection matches a target.
     * @param string $target
     * @return boolean check result
     */
    public function isTarget($target) {
        Argument::IsString($target);
        return ($this->getTarget() == $target);
    }

    /**
     * Returns the injection target name.
     * @return string the target name
     */
    public function getTargetName() {
        return $this->targetName;
    }

    /**
     * Returns the dependency arguments container.
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function getArgumentsContainer() {
        return $this->argumentsContainer;
    }

}
