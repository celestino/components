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

use Brickoo\Validation\Constraint\ContainsInstancesOfConstraint,
    PHPUnit_Framework_TestCase;

require_once "Fixture/TraversableInstancesFixture.php";

/**
 * ContainsInstancesOfConstraintTest
 *
 * Test suite for the ContainsInstancesOfConstraint class.
 * @see Brickoo\Validation\Constraint\ContainsInstancesOfConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ContainsInstancesOfConstraintTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Validation\Constraint\ContainsInstancesOfConstraint::__construct */
    public function testConstructor() {
        $expectedInstanceOf = "Traversable";
        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertInstanceOf("\\Brickoo\\Validation\\Constraint", $ContainsInstancesOfConstraint);
    }

    /**
     * @covers Brickoo\Validation\Constraint\ContainsInstancesOfConstraint::__construct
     * @expectedException InvalidArgumentException
     */
    public function testContructorTraversableThrowsInvalidArgumentException() {
        new ContainsInstancesOfConstraint(["wrongType"]);
    }

    /** @covers Brickoo\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfTraversableValues() {
        $compareFrom = new Fixture\TraversableInstancesFixture();
        $expectedInstanceOf = "Traversable";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertTrue($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /** @covers Brickoo\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfTraversableValuesFails() {
        $compareFrom = new Fixture\TraversableInstancesFixture();
        $expectedInstanceOf = "Failure";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertFalse($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /** @covers Brickoo\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfArrayValues() {
        $compareFrom = array(new \ArrayObject());
        $expectedInstanceOf = "Traversable";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertTrue($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /** @covers Brickoo\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfOneValueFails() {
        $compareFrom = array(new \stdClass());
        $expectedInstanceOf = "Traversable";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertFalse($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /**
     * @covers Brickoo\Validation\Constraint\ContainsInstancesOfConstraint::matches
     * @expectedException InvalidArgumentException
     */
    public function etstAsseertionThrowsInvalidArgumentException() {
        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint("Traversable");
        $ContainsInstancesOfConstraint->matches("wrongType");
    }

}