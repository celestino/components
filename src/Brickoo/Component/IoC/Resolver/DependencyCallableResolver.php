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

use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyResolverResultTypeException;
use Brickoo\Component\IoC\Resolver\Exception\InvalidDependencyTypeException;

/**
 * DependencyCallableResolver
 *
 * Implements a dependency callable resolver.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyCallableResolver extends  DependencyResolver {

    /** {@inheritdoc} */
    public function resolve(DependencyDefinition $dependencyDefinition) {
        $dependencyCallable = $dependencyDefinition->getDependency();

        if (! is_callable($dependencyCallable)) {
            throw new InvalidDependencyTypeException($dependencyCallable);
        }

        $dependency = call_user_func($dependencyCallable, $this->getDIContainer());

        if (! is_object($dependency)) {
            throw new InvalidDependencyResolverResultTypeException($dependency);
        }

        $this->injectDependencies($dependency, $dependencyDefinition);
        return $dependency;
    }

}
