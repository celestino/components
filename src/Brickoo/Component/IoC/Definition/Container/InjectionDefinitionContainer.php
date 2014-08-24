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
        Argument::isString($target);
        $injections = [];
        foreach ($this->getAll() as $injection) {
            if ($injection->isTarget($target)) {
                $injections[] = $injection;
            }
        }
        return $injections;
    }

}
