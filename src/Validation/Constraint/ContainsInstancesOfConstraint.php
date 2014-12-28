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

namespace Brickoo\Component\Validation\Constraint;

use Brickoo\Component\Validation\Argument;

/**
 * ContainsInstancesOfConstraint
 *
 * Constraint to assert that an array or traversable
 * contains just values of the expected instance type.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ContainsInstancesOfConstraint implements Constraint {

    /** @var string */
    private $expectedInstanceOf;

    /**
     * Class constructor.
     * @param string $expectedInstanceType the values expected type
     * @throws \InvalidArgumentException if an argument is not valid.
     */
    public function __construct($expectedInstanceType) {
        Argument::isString($expectedInstanceType);
        $this->expectedInstanceOf = $expectedInstanceType;
    }

    /**
     * {@inheritDoc}
     * @param array|\Traversable $traversable
     */
    public function matches($traversable) {
        Argument::isTraversable($traversable);

        $result = true;
        foreach ($traversable as $value) {
            if (! $value instanceof $this->expectedInstanceOf) {
                $result = false;
                break;
            }
        }
        return $result;
    }

}
