<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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
