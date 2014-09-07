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

namespace Brickoo\Component\IoC;

use Brickoo\Component\Common\Container;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Exception\DefinitionNotAvailableException;
use Brickoo\Component\IoC\Exception\InfiniteDependencyResolveLoopException;
use Brickoo\Component\IoC\Resolver\DefinitionResolver;
use Brickoo\Component\Validation\Validator\ConstraintValidator;
use Brickoo\Component\Validation\Constraint\IsInstanceOfConstraint;

/**
 * DIContainer
 *
 * Implements a dependency injection container.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DIContainer extends Container {

    /** @var \Brickoo\Component\IoC\Resolver\DefinitionResolver */
    private $resolver;

    /** @var array */
    private $calledDependencies;

    /** @var array<string, object> */
    private $singletons;

    /**
     * Class constructor.
     * Calls the parent constructor.
     * @param \Brickoo\Component\IoC\Resolver\DefinitionResolver $resolver
     * @param array $definitions
     */
    public function __construct(DefinitionResolver $resolver, array $definitions = []) {
        parent::__construct($definitions, new ConstraintValidator(
            new IsInstanceOfConstraint("\\Brickoo\\Component\\IoC\\Definition\\DependencyDefinition")
        ));
        $this->resolver = $resolver;
        $this->calledDependencies = [];
        $this->singletons = [];
    }

    /**
     * Return the definition resolver.
     * @return \Brickoo\Component\IoC\Resolver\DefinitionResolver
     */
    public function getResolver() {
        return $this->resolver;
    }

    /**
     * Return the resolved dependency.
     * @todo (PHP 5.5) replace catch block with finally
     * @param string $dependencyName
     * @throws \Brickoo\Component\IoC\Exception
     * @return object the dependency
     */
    public function retrieve($dependencyName) {
        try {
            $this->checkDependencyAccess($dependencyName);
            $definition = $this->get($dependencyName);

            if ((! $this->hasSingletonScope($definition))
                || (! $dependency = $this->getSingleton($dependencyName))) {
                    $dependency = $this->createDependency($dependencyName, $definition);
            }
        }
        catch (Exception $exception) {
            $this->calledDependencies = [];
            throw $exception;
        }

        return $dependency;
    }

    /** Check if the dependency can be accessed.
     * @param string $dependencyName
     * @throws Exception\InfiniteDependencyResolveLoopException
     * @throws Exception\DefinitionNotAvailableException
     * @return void
     */
    private function checkDependencyAccess($dependencyName) {
        if (! $this->contains($dependencyName)) {
            throw new DefinitionNotAvailableException($dependencyName);
        }

        if (isset($this->calledDependencies[$dependencyName])) {
            throw new InfiniteDependencyResolveLoopException(array_pop($this->calledDependencies));
        }
    }

    /**
     * Create the dependency object.
     * @param string $dependencyName
     * @param DependencyDefinition $definition
     * @return object
     */
    private function createDependency($dependencyName, DependencyDefinition $definition) {
        $this->calledDependencies[$dependencyName] = true;
        $dependency = $this->resolveDefinition($definition);
        unset($this->calledDependencies[$dependencyName]);

        if ($this->hasSingletonScope($definition)) {
            $this->storeSingleton($dependencyName, $dependency);
        }
        return $dependency;
    }

    private function hasSingletonScope(DependencyDefinition $definition) {
        return ($definition->getScope() == DependencyDefinition::SCOPE_SINGLETON);
    }

    /**
     * Return the resolved dependency.
     * @param DependencyDefinition $dependencyDefinition
     * @return object the defined dependency.
     */
    private function resolveDefinition(DependencyDefinition $dependencyDefinition) {
        return $this->getResolver()->resolve($this, $dependencyDefinition);
    }

    /**
     * Return the stored singleton dependency.
     * @param string $dependencyName
     * @return mixed the dependency otherwise null
     */
    private function getSingleton($dependencyName) {
        if (isset($this->singletons[$dependencyName])) {
            return $this->singletons[$dependencyName];
        }
        return null;
    }

    /**
     * Store the dependency as singleton.
     * @param string $dependencyName
     * @param object $dependency
     * @return \Brickoo\Component\IoC\DIContainer
     */
    private function storeSingleton($dependencyName, $dependency) {
        if (! isset($this->singletons[$dependencyName])) {
            $this->singletons[$dependencyName] = $dependency;
        }
        return $this;
    }

}
