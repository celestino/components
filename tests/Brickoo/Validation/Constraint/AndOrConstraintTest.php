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

namespace Brickoo\Tests\Validation\Constraint;

use Brickoo\Validation\Constraint\AndOrConstraint,
    Brickoo\Validation\Constraint\IsEqualToConstraint,
    Brickoo\Validation\Constraint\IsTypeConstraint,
    PHPUnit_Framework_TestCase;

/**
 * AndOrConstraint
 *
 * Test suite for the AndOrConstraint class.
 * @see Brickoo\Validation\Constraint\AndOrConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AndOrConstraintTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::__construct
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::doesConstraitGroupMatch
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testMatchingValueWithConstraintGroupUsingAND() {
        $andOrConstraint = new AndOrConstraint([
            new IsEqualToConstraint("12345"),
            new IsTypeConstraint("string")
        ]);
        $this->assertTrue($andOrConstraint->matches("12345"));
        $this->assertFalse($andOrConstraint->matches(12345));
    }

    /**
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::doesConstraitGroupMatch
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testMatchingValueWithConstraintGroupUsingOR() {
        $andOrConstraint = new AndOrConstraint(
            [new IsEqualToConstraint("test")],
            [new IsTypeConstraint("string")]
        );
        $this->assertTrue($andOrConstraint->matches("test"));
        $this->assertTrue($andOrConstraint->matches("otherString"));
        $this->assertFalse($andOrConstraint->matches(12345));
    }

    /**
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::getFailedConstraint
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::doesConstraitGroupMatch
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testGetFailedConstrait() {
        $andOrConstraint = new AndOrConstraint(
            [$constrait = new IsTypeConstraint("string")]
        );
        $this->assertFalse($andOrConstraint->matches(12345));
        $this->assertSame($constrait, $andOrConstraint->getFailedConstraint());
    }

    /**
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::getFailedConstraint
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::matches
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::doesConstraitGroupMatch
     * @covers Brickoo\Validation\Constraint\AndOrConstraint::getConcreteFailedConstraint
     */
    public function testGetFailedConstraitFromNestedGroup() {
        $andOrConstraint = new AndOrConstraint(
            [new AndOrConstraint([
                $constrait = new IsTypeConstraint("string")
            ])]
        );
        $this->assertFalse($andOrConstraint->matches(12345));
        $this->assertSame($constrait, $andOrConstraint->getFailedConstraint());
    }

}