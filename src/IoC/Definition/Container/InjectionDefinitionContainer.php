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

use Brickoo\Component\IoC\Definition\InjectionDefinition;
use Brickoo\Component\IoC\Definition\Container\Exception\DuplicateInjectionDefinitionException;
use Brickoo\Component\Common\Assert;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * InjectionDefinitionContainer
 *
 * Implements a container for injection definitions.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InjectionDefinitionContainer extends DefinitionContainer {

    /**
     * Class constructor
     * @param \Brickoo\Component\IoC\Definition\InjectionDefinition[] $injections
     */
    public function __construct(array $injections = []) {
        $this->setInjections($injections);
    }

    /**
     * Adds an injection to the dependency definition.
     * @param \Brickoo\Component\IoC\Definition\InjectionDefinition $injection
     * @throws \Brickoo\Component\IoC\Definition\Container\Exception\DuplicateInjectionDefinitionException
     * @return \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer
     */
    public function addInjection(InjectionDefinition $injection) {
        if ($this->contains(($targetName = $injection->getTargetName()))) {
            throw new DuplicateInjectionDefinitionException($targetName);
        }
        $this->add($targetName, $injection);
        return $this;
    }

    /**
     * Sets the list of injections to the dependency definition.
     * @param InjectionDefinition[] $injections
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer
     */
    public function setInjections($injections) {
        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\IoC\\Definition\\InjectionDefinition"))->matches($injections)) {
            throw new \InvalidArgumentException(
                "The definition injections keys must be of type `\\Brickoo\\Component\\IoC\\Definition\\InjectionDefinition`."
            );
        }
        foreach ($injections as $injection) {
            $this->addInjection($injection);
        }
        return $this;
    }

    /**
     * Returns the injection definition matching a target.
     * @param string $target
     * @return array the target matching injection definitions.
     */
    public function getByTarget($target) {
        Assert::isString($target);
        $injections = [];
        foreach ($this->getAll() as $injection) {
            if ($injection->isTarget($target)) {
                $injections[] = $injection;
            }
        }
        return $injections;
    }

}
