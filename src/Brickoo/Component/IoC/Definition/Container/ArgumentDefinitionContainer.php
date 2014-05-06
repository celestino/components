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

use Brickoo\Component\IoC\Definition\ArgumentDefinition,
    Brickoo\Component\IoC\Definition\Container\Exception\ArgumentNotAvailableException,
    Brickoo\Component\IoC\Definition\Container\Exception\DuplicateParameterDefinitionException,
    Brickoo\Component\Validation\Argument,
    Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * ArgumentDefinitionContainer
 *
 * Implements a container for argument definitions.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ArgumentDefinitionContainer implements \IteratorAggregate, \Countable {

    /** @var array<string, \Brickoo\Component\IoC\Definition\ArgumentDefinition> */
    private $arguments;

    /**
     * Class constructor.
     * @param array<\Brickoo\Component\IoC\Definition\ArgumentDefinition> $arguments
     */
    public function __construct(array $arguments = []) {
        $this->arguments = [];
        $this->set($arguments);
    }

    /**
     * Checks if the definition container is empty.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->arguments);
    }

    /**
     * Checks if the definition contains an argument.
     * @param string $argumentName
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function contains($argumentName) {
        Argument::IsString($argumentName);
        return isset($this->arguments[$argumentName]);
    }

    /**
     * Adds an argument to the dependency definition.
     * @param \Brickoo\Component\IoC\Definition\ArgumentDefinition $argument
     * @throws \Brickoo\Component\IoC\Definition\Container\Exception\DuplicateParameterDefinitionException
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function add(ArgumentDefinition $argument) {
        if (($hasName = $argument->hasName()) && $this->contains($argument->getName())) {
            throw new DuplicateParameterDefinitionException($argument->getName());
        }

        $argumentKey = $hasName ? $argument->getName() : uniqid("arg:");
        $this->arguments[$argumentKey] = $argument;
        return $this;
    }

    /**
     * Sets the list of arguments to the dependency definition.
     * @param \Traversable|array<\Brickoo\Component\IoC\Definition\ArgumentDefinition> $arguments
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function set($arguments) {
        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\IoC\\Definition\\ArgumentDefinition"))->matches($arguments)) {
            throw new \InvalidArgumentException(
                "The definition arguments keys must be of type `\\Brickoo\\Component\\IoC\\Definition\\ArgumentDefinition`."
            );
        }

        foreach ($arguments as $argument) {
            $this->add($argument);
        }
        return $this;
    }

    /**
     * Removes an argument from the container.
     * @param string $argumentName
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function remove($argumentName) {
        Argument::IsString($argumentName);

        if ($this->contains($argumentName)) {
            unset($this->arguments[$argumentName]);
        }

        return $this;
    }

    /**
     * Returns an argument definition by its name.
     * @param string $argumentName
     * @throws \Brickoo\Component\IoC\Definition\Container\Exception\ArgumentNotAvailableException
     * @return \Brickoo\Component\IoC\Definition\ArgumentDefinition
     */
    public function get($argumentName) {
        Argument::IsString($argumentName);

        if (! $this->contains($argumentName)) {
            throw new ArgumentNotAvailableException($argumentName);
        }

        return $this->arguments[$argumentName];
    }

    /**
     * Returns all argument definitions.
     * @return array<\Brickoo\Component\IoC\Definition\ArgumentDefinition>
     */
    public function getAll() {
        return array_values($this->arguments);
    }

    /**
     * Retrieve an array iterator.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator containing the arguments
     */
    public function getIterator() {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * Count container arguments.
     * @link http://php.net/manual/en/countable.count.php
     * @return integer the amount of arguments
     */
    public function count() {
        return count($this->arguments);
    }

}
