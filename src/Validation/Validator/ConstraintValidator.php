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

namespace Brickoo\Component\Validation\Validator;

use Brickoo\Component\Validation\Constraint\AndOrConstraint;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;
use InvalidArgumentException;

/**
 * Validator
 *
 * Implements a validator for a value against constraint.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ConstraintValidator implements Validator {

    /** @var \Brickoo\Component\Validation\Constraint\AndOrConstraint */
    private $constraintGroup;

    /**
     * Class constructor.
     * Can get any number of parameters with the type Constraint.
     * All constraints must match to succeed validation.
     * @param Constraint ...params
     * @throws \InvalidArgumentException
     */
    public function __construct() {
        $constraints = func_get_args();
        if (!(new ContainsInstancesOfConstraint("\\Brickoo\\Component\\Validation\\Constraint\\Constraint"))->matches($constraints)) {
            throw new InvalidArgumentException("Instances of \\Brickoo\\Component\\Validation\\Constraint\\Constraint expected.");
        }
        $this->constraintGroup = new AndOrConstraint($constraints);
    }

    /** {@inheritDoc} */
    public function isValid($value) {
        return $this->constraintGroup->matches($value);
    }

    /**
     * Returns the last constraint which did not match.
     * @return null|\Brickoo\Component\Validation\Constraint\Constraint otherwise null
     */
    public function getFailedConstraint() {
        return $this->constraintGroup->getFailedConstraint();
    }

}
