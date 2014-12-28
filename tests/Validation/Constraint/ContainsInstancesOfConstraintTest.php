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

namespace Brickoo\Tests\Component\Validation\Constraint;

use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;
use PHPUnit_Framework_TestCase;

require_once "Fixture/TraversableInstancesFixture.php";

/**
 * ContainsInstancesOfConstraintTest
 *
 * Test suite for the ContainsInstancesOfConstraint class.
 * @see Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ContainsInstancesOfConstraintTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint::__construct */
    public function testConstructor() {
        $expectedInstanceOf = "Traversable";
        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertInstanceOf("\\Brickoo\\Component\\Validation\\Constraint\\Constraint", $ContainsInstancesOfConstraint);
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testContructorTraversableThrowsInvalidArgumentException() {
        new ContainsInstancesOfConstraint(["wrongType"]);
    }

    /** @covers Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfTraversableValues() {
        $compareFrom = new Fixture\TraversableInstancesFixture();
        $expectedInstanceOf = "Traversable";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertTrue($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /** @covers Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfTraversableValuesFails() {
        $compareFrom = new Fixture\TraversableInstancesFixture();
        $expectedInstanceOf = "Failure";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertFalse($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /** @covers Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfArrayValues() {
        $compareFrom = array(new \ArrayObject());
        $expectedInstanceOf = "Traversable";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertTrue($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /** @covers Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint::matches */
    public function testAssertionOfOneValueFails() {
        $compareFrom = array(new \stdClass());
        $expectedInstanceOf = "Traversable";

        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint($expectedInstanceOf);
        $this->assertFalse($ContainsInstancesOfConstraint->matches($compareFrom));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint::matches
     * @expectedException \InvalidArgumentException
     */
    public function testAssertionThrowsInvalidArgumentException() {
        $ContainsInstancesOfConstraint = new ContainsInstancesOfConstraint("Traversable");
        $ContainsInstancesOfConstraint->matches("wrongType");
    }

}
