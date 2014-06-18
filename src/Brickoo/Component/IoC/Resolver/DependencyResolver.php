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

namespace Brickoo\Component\IoC\Resolver;

use Brickoo\Component\IoC\Definition\ArgumentDefinition,
    Brickoo\Component\IoC\Definition\DependencyDefinition,
    Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer,
    Brickoo\Component\IoC\Definition\InjectionDefinition,
    Brickoo\Component\IoC\DIContainer;

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
    public function getDIContainer() {
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
        foreach ($arguments as $argument) {
            $argumentIndex = $this->getArgumentIndex($argument, count($collectedArguments));
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

        if ($argumentValue instanceof \Closure) {
            $argumentValue = $argumentValue->bindTo($this->getDIContainer());
        }

        if (is_callable($argumentValue)) {
            return call_user_func($argumentValue);
        }

        if (is_string($argumentValue)
            && strpos($argumentValue, $this->definitionPrefix) === 0
        ) {
            return $this->getDIContainer()->retrieve(
                substr($argumentValue, strlen($this->definitionPrefix))
            );
        }

        return $argumentValue;
    }

    /**
     * Injects the target object dependencies.
     * @param object $targetObject
     * @param \Brickoo\Component\IoC\Definition\DependencyDefinition $dependencyDefinition
     * @return \Brickoo\Component\IoC\Resolver\DefinitionResolver
     */
    protected function injectDependencies($targetObject, DependencyDefinition $dependencyDefinition) {
        $injectionsContainer = $dependencyDefinition->getInjectionsContainer();
        if ($injectionsContainer->isEmpty()) {
            return $targetObject;
        }

        if (($injectionDefinitions =  $injectionsContainer->getByTarget(InjectionDefinition::TARGET_METHOD))) {
            $this->injectDependenciesToMethods($targetObject, $injectionDefinitions);
        }

        if (($injectionDefinitions =  $injectionsContainer->getByTarget(InjectionDefinition::TARGET_PROPERTY))) {
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
