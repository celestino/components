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
