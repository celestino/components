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
