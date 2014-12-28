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
     * @param null|\Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer $argumentsContainer
     * @param null|\Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer $injectionsContainer
     */
    public function __construct(
        $dependency,
        $scope = self::SCOPE_PROTOTYPE,
        ArgumentDefinitionContainer $argumentsContainer = null,
        InjectionDefinitionContainer $injectionsContainer = null) {
            Argument::isInteger($scope);
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
