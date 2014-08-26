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

use Brickoo\Component\IoC\DIContainer;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Resolver\Exception\DefinitionTypeUnknownException;
use Brickoo\Component\Validation\Argument;

/**
 * DefinitionResolver
 *
 * Defines an abstract dependency definition resolver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DefinitionResolver {

    /** Unsupported definition type. */
    const TYPE_UNSUPPORTED = 0;

    /** Dependency is of type class */
    const TYPE_CLASS = 1;

    /** Dependency is of type closure */
    const TYPE_CLOSURE = 2;

    /** Dependency is of type object */
    const TYPE_OBJECT = 3;

    /** Dependency is of type callable */
    const TYPE_CALLABLE = 4;

    /** @var array<string,\Brickoo\Component\IoC\Resolver\DependencyResolver> */
    private $resolvers;

    public function __construct() {
        $this->resolvers = array();
    }

    /**
     * Resolves a dependency definition.
     * @param \Brickoo\Component\IoC\DIContainer $container
     * @param \Brickoo\Component\IoC\Definition\DependencyDefinition
     * @throws \Brickoo\Component\IoC\Resolver\Exception\DefinitionTypeUnknownException
     * @return mixed the resolved definition result
     */
    public function resolve(DIContainer $container, DependencyDefinition $dependencyDefinition) {
        $dependency = $dependencyDefinition->getDependency();
        return $this->getResolver($this->getResolverType($dependency), $container)->resolve($dependencyDefinition);
    }

    /**
     * Sets a resolver for an explicit type.
     * @param integer $resolverType
     * @param \Brickoo\Component\IoC\Resolver\DependencyResolver $resolver
     * @return \Brickoo\Component\IoC\Resolver\DefinitionResolver
     */
    public function setResolver($resolverType, DependencyResolver $resolver) {
        Argument::isInteger($resolverType);
        $this->resolvers[$resolverType] = $resolver;
        return $this;
    }

    /**
     * Returns the corresponding type resolver.
     * @param string $definitionType
     * @param \Brickoo\Component\IoC\DIContainer $diContainer
     * @throws \Brickoo\Component\IoC\Resolver\Exception\DefinitionTypeUnknownException
     * @return \Brickoo\Component\IoC\Resolver\DependencyResolver
     */
    private function getResolver($definitionType, DIContainer $diContainer) {
        if (array_key_exists($definitionType, $this->resolvers)) {
            return $this->resolvers[$definitionType];
        }

        $resolver = $this->getDefinitionResolverByType($definitionType, $diContainer);
        $this->resolvers[$definitionType] = $resolver;
        return $resolver;
    }

    /**
     * Returns the corresponding type resolver.
     * @param mixed $dependency
     * @return integer
     */
    private function getResolverType($dependency) {
        if ($dependency instanceof \Closure) {
            return self::TYPE_CLOSURE;
        }

        if (is_object($dependency)) {
            return self::TYPE_OBJECT;
        }

        if (is_callable($dependency)) {
            return self::TYPE_CALLABLE;
        }

        if (is_string($dependency) && class_exists($dependency)) {
            return self::TYPE_CLASS;
        }

        return self::TYPE_UNSUPPORTED;
    }

    /**
     * Return the corresponding resolver by type.
     * @param integer $definitionType
     * @param DIContainer $diContainer
     * @throws \Brickoo\Component\IoC\Resolver\Exception\DefinitionTypeUnknownException
     * @return \Brickoo\Component\IoC\Resolver\DependencyResolver
     */
    private function getDefinitionResolverByType($definitionType, DIContainer $diContainer) {
        $resolvers = [
            self::TYPE_CLOSURE => "\\DependencyClosureResolver",
            self::TYPE_OBJECT => "\\DependencyObjectResolver",
            self::TYPE_CALLABLE => "\\DependencyCallableResolver",
            self::TYPE_CLASS => "\\DependencyClassResolver"
        ];

        if (! isset($resolvers[$definitionType])) {
            throw new DefinitionTypeUnknownException($definitionType);
        }

        $className = __NAMESPACE__.$resolvers[$definitionType];
        return new $className($diContainer);
    }

}
