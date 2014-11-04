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

namespace Brickoo\Tests\Component\Validation\Constraint;

use Brickoo\Component\Validation\Constraint\AndOrConstraint;
use Brickoo\Component\Validation\Constraint\IsEqualToConstraint;
use Brickoo\Component\Validation\Constraint\IsInternalTypeConstraint;
use PHPUnit_Framework_TestCase;

/**
 * AndOrConstraint
 *
 * Test suite for the AndOrConstraint class.
 * @see Brickoo\Component\Validation\Constraint\AndOrConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AndOrConstraintTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::__construct
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::removeFailures
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::doesConstraintGroupMatch
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testMatchingValueWithConstraintGroupUsingAND() {
        $andOrConstraint = new AndOrConstraint([
            new IsEqualToConstraint("12345"),
            new IsInternalTypeConstraint("string")
        ]);
        $this->assertTrue($andOrConstraint->matches("12345"));
        $this->assertFalse($andOrConstraint->matches(12345));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::removeFailures
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::doesConstraintGroupMatch
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testMatchingValueWithConstraintGroupUsingOR() {
        $andOrConstraint = new AndOrConstraint(
            [new IsEqualToConstraint("test")],
            [new IsInternalTypeConstraint("string")]
        );
        $this->assertTrue($andOrConstraint->matches("test"));
        $this->assertTrue($andOrConstraint->matches("otherString"));
        $this->assertFalse($andOrConstraint->matches(12345));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::getFailedConstraint
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::doesConstraintGroupMatch
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testGetFailedConstraint() {
        $andOrConstraint = new AndOrConstraint(
            [$constraint = new IsInternalTypeConstraint("string")]
        );
        $this->assertFalse($andOrConstraint->matches(12345));
        $this->assertSame($constraint, $andOrConstraint->getFailedConstraint());
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::getFailedConstraint
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::doesConstraintGroupMatch
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testGetFailedConstraintFromNestedGroup() {
        $andOrConstraint = new AndOrConstraint(
            [new AndOrConstraint([
                $constraint = new IsInternalTypeConstraint("string")
            ])]
        );
        $this->assertFalse($andOrConstraint->matches(12345));
        $this->assertSame($constraint, $andOrConstraint->getFailedConstraint());
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\AndOrConstraint::removeFailures
     */
    public function testRemoveFailures() {
        $andOrConstraint = new AndOrConstraint(
            [$constraint = new IsInternalTypeConstraint("string")]
        );
        $this->assertFalse($andOrConstraint->matches(12345));
        $this->assertSame($constraint, $andOrConstraint->getFailedConstraint());

        $this->assertTrue($andOrConstraint->matches("success"));
        $this->assertNull($andOrConstraint->getFailedConstraint());
    }

}
