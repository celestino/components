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
