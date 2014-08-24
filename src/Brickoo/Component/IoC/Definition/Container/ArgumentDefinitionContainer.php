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

use Brickoo\Component\IoC\Definition\ArgumentDefinition;
use Brickoo\Component\IoC\Definition\Container\Exception\DuplicateParameterDefinitionException;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * ArgumentDefinitionContainer
 *
 * Implements a container for argument definitions.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ArgumentDefinitionContainer extends DefinitionContainer {

    /**
     * Class constructor.
     * @param \Brickoo\Component\IoC\Definition\ArgumentDefinition[] $arguments
     */
    public function __construct(array $arguments = []) {
        $this->setArguments($arguments);
    }

    /**
     * Adds an argument to the dependency definition.
     * @param \Brickoo\Component\IoC\Definition\ArgumentDefinition $argument
     * @throws \Brickoo\Component\IoC\Definition\Container\Exception\DuplicateParameterDefinitionException
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function addArgument(ArgumentDefinition $argument) {
        if (($hasName = $argument->hasName()) && $this->contains($argument->getName())) {
            throw new DuplicateParameterDefinitionException($argument->getName());
        }

        $argumentKey = $hasName ? $argument->getName() : uniqid("arg:");
        $this->add($argumentKey, $argument);
        return $this;
    }

    /**
     * Sets the list of arguments to the dependency definition.
     * @param ArgumentDefinition[] $arguments
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer
     */
    public function setArguments($arguments) {
        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\IoC\\Definition\\ArgumentDefinition"))->matches($arguments)) {
            throw new \InvalidArgumentException(
                "The definition arguments keys must be of type `\\Brickoo\\Component\\IoC\\Definition\\ArgumentDefinition`."
            );
        }

        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
        return $this;
    }

}
