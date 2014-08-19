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
use Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer;
use Brickoo\Component\Validation\Argument;

/**
 * DependencyDefinition
 *
 * Implements a dependency definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyDefinition {

    /** Scope definitions */
    const SCOPE_SINGLETON = 1;
    const SCOPE_PROTOTYPE = 2;

    /** @var \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer */
    private $argumentsContainer;

    /** @var \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer */
    private $injectionsContainer;

    /** @var mixed */
    private $dependency;

    /** @var integer */
    private $scope;

    /**
     * Class constructor.
     * @param mixed $dependency
     * @param integer $scope
     * @param \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer $argumentsContainer
     * @param \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer $injectionsContainer
     */
    public function __construct(
        $dependency,
        $scope = self::SCOPE_PROTOTYPE,
        ArgumentDefinitionContainer $argumentsContainer = null,
        InjectionDefinitionContainer $injectionsContainer = null
    ) {
        Argument::IsInteger($scope);
        $this->scope = $scope;
        $this->setDependency($dependency);
        $this->argumentsContainer = $argumentsContainer ?: new ArgumentDefinitionContainer();
        $this->injectionsContainer = $injectionsContainer ?: new InjectionDefinitionContainer();
    }

    /**
     * Return the dependency scope.
     * @return integer the dependency scope
     */
    public function getScope() {
        return $this->scope;
    }

    /**
     * Return the dependency arguments definitions container.
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function getArgumentsContainer() {
        return $this->argumentsContainer;
    }

    /**
     * Return the dependency injection definitions container.
     * @return \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer
     */
    public function getInjectionsContainer() {
        return $this->injectionsContainer;
    }

    /**
     * Return the dependency provided.
     * @return mixed
     */
    public function getDependency() {
        return $this->dependency;
    }

    /**
     * Set the dependency of the definition.
     * @param mixed $dependency
     * @return \Brickoo\Component\IoC\Definition\DependencyDefinition
     */
    public function setDependency($dependency) {
        $this->dependency = $dependency;
        return $this;
    }

}
