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

namespace Brickoo\Component\IoC\Resolver;

use Brickoo\Component\IoC\DIContainer;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Resolver\Exception\DefinitionTypeUnknownException;
use Brickoo\Component\Common\Assert;

/**
 * DefinitionResolver
 *
 * Defines an abstract dependency definition resolver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DefinitionResolver {

    /** Unsupported definition type. */
    const TYPE_UNSUPPORTED = "unsupported";

    /** Dependency is of type class */
    const TYPE_CLASS = "class";

    /** Dependency is of type closure */
    const TYPE_CLOSURE = "closure";

    /** Dependency is of type object */
    const TYPE_OBJECT = "object";

    /** Dependency is of type callable */
    const TYPE_CALLABLE = "callable";

    /** @var array */
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
     * @param string $resolverType
     * @param \Brickoo\Component\IoC\Resolver\DependencyResolver $resolver
     * @return \Brickoo\Component\IoC\Resolver\DefinitionResolver
     */
    public function setResolver($resolverType, DependencyResolver $resolver) {
        Assert::isString($resolverType);
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
     * @return string
     */
    private function getResolverType($dependency) {
        $matchingTypes = array_filter(
            [
                self::TYPE_CLOSURE => ($dependency instanceof \Closure),
                self::TYPE_OBJECT => is_object($dependency),
                self::TYPE_CALLABLE => is_callable($dependency),
                self::TYPE_CLASS => (is_string($dependency) && class_exists($dependency)),
                self::TYPE_UNSUPPORTED => true
            ],
            function($value) {return $value === true;}
        );

        $types = array_keys($matchingTypes);
        return array_shift($types);
    }

    /**
     * Return the corresponding resolver by type.
     * @param string $definitionType
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

        if (!isset($resolvers[$definitionType])) {
            throw new DefinitionTypeUnknownException($definitionType);
        }

        $className = __NAMESPACE__.$resolvers[$definitionType];
        return new $className($diContainer);
    }

}
