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

namespace Brickoo\Component\IoC\Resolver;

use Brickoo\Component\IoC\Definition\ArgumentDefinition;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer;
use Brickoo\Component\IoC\Definition\InjectionDefinition;
use Brickoo\Component\IoC\DIContainer;

/**
 * DependencyResolver
 *
 * Defines an abstract dependency resolver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
abstract class DependencyResolver {

    /** @var string */
    protected $definitionPrefix;

    /** @var \Brickoo\Component\IoC\DIContainer */
    protected $diContainer;

    /**
     * Class constructor.
     * @param \Brickoo\Component\IoC\DIContainer $diContainer
     * @param string $dependencyPrefix
     */
    public function __construct(DIContainer $diContainer, $dependencyPrefix = "@") {
        $this->diContainer = $diContainer;
        $this->definitionPrefix = $dependencyPrefix;
    }

    /**
     * Returns the DI container instance.
     * @return \Brickoo\Component\IoC\DIContainer
     */
    public function getDiContainer() {
        return $this->diContainer;
    }

    /**
     * Resolves a dependency definition.
     * @param \Brickoo\Component\IoC\Definition\DependencyDefinition
     * @throws \Brickoo\Component\IoC\Exception
     * @return mixed the resolved definition result
     */
    abstract public function resolve(DependencyDefinition $dependencyDefinition);

    /**
     * Collects the arguments from an argument definition.
     * @param \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer $argumentsContainer
     * @return array the collected arguments
     */
    protected function collectArguments(ArgumentDefinitionContainer $argumentsContainer) {
        if ($argumentsContainer->isEmpty()) {
            return [];
        }

        $collectedArguments = [];
        $arguments = $argumentsContainer->getAll();
        foreach ($arguments as $index => $argument) {
            $argumentIndex = $this->getArgumentIndex($argument, $index);
            $collectedArguments[$argumentIndex] = $this->getArgumentValue($argument);
        }
        return $collectedArguments;
    }

    /**
     * Returns the argument index.
     * @param \Brickoo\Component\IoC\Definition\ArgumentDefinition $argument
     * @param integer $currentIndex
     * @return string|integer the argument index
     */
    private function getArgumentIndex(ArgumentDefinition $argument, $currentIndex) {
        return $argument->hasName() ? $argument->getName() : $currentIndex;
    }

    /**
     * Returns the argument definition resolved value.
     * @param \Brickoo\Component\IoC\Definition\ArgumentDefinition $argument
     * @return mixed the argument value
     */
    private function getArgumentValue(ArgumentDefinition $argument) {
        $argumentValue = $argument->getValue();

        if (is_callable($argumentValue)) {
            return call_user_func($argumentValue, $this->getDiContainer());
        }

        if (is_string($argumentValue)
            && strpos($argumentValue, $this->definitionPrefix) === 0
        ) {
            return $this->getDiContainer()->retrieve(
                substr($argumentValue, strlen($this->definitionPrefix))
            );
        }

        return $argumentValue;
    }

    /**
     * Injects the target object dependencies.
     * @param object $targetObject
     * @param \Brickoo\Component\IoC\Definition\DependencyDefinition $dependencyDefinition
     * @return \Brickoo\Component\IoC\Resolver\DependencyResolver
     */
    protected function injectDependencies($targetObject, DependencyDefinition $dependencyDefinition) {
        $injectionsContainer = $dependencyDefinition->getInjectionsContainer();
        if ($injectionsContainer->isEmpty()) {
            return $targetObject;
        }

        if (($injectionDefinitions = $injectionsContainer->getByTarget(InjectionDefinition::TARGET_METHOD))) {
            $this->injectDependenciesToMethods($targetObject, $injectionDefinitions);
        }

        if (($injectionDefinitions = $injectionsContainer->getByTarget(InjectionDefinition::TARGET_PROPERTY))) {
            $this->injectDependenciesToProperties($targetObject, $injectionDefinitions);
        }

        return $this;
    }

    /**
     * Injects the dependencies to the corresponding properties.
     * @param object $targetObject
     * @param array $injectionDefinitions
     * @return void
     */
    private function injectDependenciesToProperties($targetObject, array $injectionDefinitions) {
        $injectionDefinition = $injectionDefinitions[0];
        $targetProperty = $injectionDefinition->getTargetName();
        if (property_exists($targetObject, $targetProperty)) {
            $arguments = $this->collectArguments($injectionDefinition->getArgumentsContainer());
            $targetObject->{$targetProperty} = array_shift($arguments);
        }
    }

    /**
     * Injects the dependencies to the corresponding methods.
     * @param object $targetObject
     * @param array $injectionDefinitions
     * @return void
     */
    private function injectDependenciesToMethods($targetObject, array $injectionDefinitions) {
        foreach ($injectionDefinitions as $injectionDefinition) {
            $targetMethod = $injectionDefinition->getTargetName();
            if (method_exists($targetObject, $targetMethod)) {
                call_user_func_array([$targetObject, $targetMethod], $this->collectArguments($injectionDefinition->getArgumentsContainer()));
            }
        }
    }

}
