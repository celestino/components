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

namespace Brickoo\Component\IoC\Definition\Container;

use Brickoo\Component\IoC\Definition\InjectionDefinition;
use Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException;
use Brickoo\Component\IoC\Definition\Container\Exception\DuplicateInjectionDefinitionException;
use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * InjectionDefinitionContainer
 *
 * Implements a container for injection definitions.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InjectionDefinitionContainer implements \IteratorAggregate, \Countable {

    /** @var array<string, \Brickoo\Component\IoC\Definition\InjectionDefinition> */
    private $injections;

    /**
     * Class constructor
     * @param array<\Brickoo\Component\IoC\Definition\InjectionDefinition> $injections
     */
    public function __construct(array $injections = []) {
        $this->injections = [];
        $this->set($injections);
    }

    /**
     * Checks if the definition has not injections.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->injections);
    }

    /**
     * Checks if the definition contains an injection.
     * @param string $injectionTargetName
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function contains($injectionTargetName) {
        Argument::IsString($injectionTargetName);
        return isset($this->injections[$injectionTargetName]);
    }

    /**
     * Adds an injection to the dependency definition.
     * @param \Brickoo\Component\IoC\Definition\InjectionDefinition $injection
     * @throws \Brickoo\Component\IoC\Definition\Container\Exception\DuplicateInjectionDefinitionException
     * @return \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer
     */
    public function add(InjectionDefinition $injection) {
        if ($this->contains(($targetName = $injection->getTargetName()))) {
            throw new DuplicateInjectionDefinitionException($targetName);
        }
        $this->injections[$targetName] = $injection;
        return $this;
    }

    /**
     * Sets the list of injections to the dependency definition.
     * @param InjectionDefinition[] $injections
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer
     */
    public function set($injections) {
        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\IoC\\Definition\\InjectionDefinition"))->matches($injections)) {
            throw new \InvalidArgumentException(
                "The definition injections keys must be of type `\\Brickoo\\Component\\IoC\\Definition\\InjectionDefinition`."
            );
        }
        foreach ($injections as $injection) {
            $this->add($injection);
        }
        return $this;
    }

    /**
     * Removes an injection definition from container.
     * @param string $injectionName
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer
     */
    public function remove($injectionName) {
        if ($this->contains($injectionName)) {
            unset($this->injections[$injectionName]);
        }
        return $this;
    }

    /**
     * Returns an injection definition by its name.
     * @param string $injectionTargetName
     * @throws \Brickoo\Component\IoC\Definition\Container\Exception\DefinitionNotAvailableException
     * @return \Brickoo\Component\IoC\Definition\InjectionDefinition
     */
    public function get($injectionTargetName) {
        if (! $this->contains($injectionTargetName)) {
            throw new DefinitionNotAvailableException($injectionTargetName);
        }
        return $this->injections[$injectionTargetName];
    }

    /**
     * Returns the available injection definitions.
     * @return array<\Brickoo\Component\IoC\Definition\InjectionDefinition>
     */
    public function getAll() {
        return array_values($this->injections);
    }

    /**
     * Returns the injection definition matching a target.
     * @param string $target
     * @return array the target matching injection definitions.
     */
    public function getByTarget($target) {
        Argument::IsString($target);
        $injections = [];
        foreach ($this->getAll() as $injection) {
            if ($injection->isTarget($target)) {
                $injections[] = $injection;
            }
        }
        return $injections;
    }

    /**
     * Retrieve an array iterator containing the injections.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * Count injection in the container.
     * @link http://php.net/manual/en/countable.count.php
     * @return integer the amount of injections
     */
    public function count() {
        return count($this->injections);
    }

}
