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

        if (!is_callable($dependencyCallable)) {
            throw new InvalidDependencyTypeException($dependencyCallable);
        }

        $dependency = call_user_func($dependencyCallable, $this->getDIContainer());

        if (!is_object($dependency)) {
            throw new InvalidDependencyResolverResultTypeException($dependency);
        }

        $this->injectDependencies($dependency, $dependencyDefinition);
        return $dependency;
    }

}
