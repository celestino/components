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

/**
 * AndOrConstraint
 *
 * Constraint to group AND and OR constraints which can be nested.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AndOrConstraint implements Constraint {

    /** @var array */
    private $constraints;

    /** @var null|\Brickoo\Component\Validation\Constraint\Constraint */
    private $failedConstraint;

    /**
     * Class constructor.
     * Can be called with multiple arguments of type array.
     * Each param array represents an AND group while
     * each param represents an OR group.
     * Example: new AndOrConstraint([constraint1, constraint2], [constraint3])
     * Either constraint1 AND constraint2 OR constraint3 MUST match.
     * @param array<Constraint> ...params
     */
    public function __construct() {
        $this->constraints = func_get_args();
    }

    /** {@inheritDoc} */
    public function matches($value) {
        $this->removeFailures();

        $matches = true;
        foreach ($this->constraints as $constraintGroup) {
            if (($matches = $this->doesConstraintGroupMatch($constraintGroup, $value))) {
                break;
            }
        }
        return $matches;
    }

    /**
     * Returns the last constraint which did not match.
     * @return null|\Brickoo\Component\Validation\Constraint\Constraint
     */
    public function getFailedConstraint() {
        return $this->failedConstraint;
    }

    /**
     * Remove the failures if any.
     * @return \Brickoo\Component\Validation\Constraint\AndOrConstraint
     */
    private function removeFailures() {
        $this->failedConstraint = null;
        return $this;
    }

    /**
     * Checks if a group of constraints do all match.
     * @param array $constraintGroup
     * @param mixed $value
     * @return boolean check result
     */
    private function doesConstraintGroupMatch(array $constraintGroup, $value) {
        $matches = true;
        foreach ($constraintGroup as $constraint) {
            if (! $constraint->matches($value)) {
                $this->failedConstraint = $this->getConcreteFailedConstraint($constraint);
                $matches = false;
                break;
            }
        }
        return $matches;
    }

    /**
     * Returns the concrete failed constraint.
     * @param \Brickoo\Component\Validation\Constraint\Constraint $constraint
     * @return null|\Brickoo\Component\Validation\Constraint\Constraint
     */
    private function getConcreteFailedConstraint(Constraint $constraint) {
        if ($constraint instanceof AndOrConstraint) {
            return $constraint->getFailedConstraint();
        }
        return $constraint;
    }

}
