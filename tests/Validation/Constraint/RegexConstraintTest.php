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

use Brickoo\Component\Validation\Constraint\RegexConstraint;
use PHPUnit_Framework_TestCase;

/**
 * RegexConstraintTest
 *
 * Test suite for the RegexConstraint class.
 * @see Brickoo\Component\Validation\RegexConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RegexConstraintTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Validation\Constraint\RegexConstraint::__construct */
    public function testConstructor() {
        $regularExpression = "~^[a-z\s]+$~";
        $RegexConstraint = new RegexConstraint($regularExpression);
        $this->assertInstanceOf("\\Brickoo\\Component\\Validation\\Constraint\\Constraint", $RegexConstraint);
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\RegexConstraint::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsInvalidArgumentException() {
        new RegexConstraint(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\RegexConstraint::matches
     */
    public function testAssertRegularExpression() {
        $compareWith ="test case";
        $regularExpression = "~^[a-z\s]+$~";

        $RegexConstraint = new RegexConstraint($regularExpression);
        $this->assertTrue($RegexConstraint->matches($compareWith));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\RegexConstraint::matches
     */
    public function testAssertRegularExpressionFails() {
        $compareWith ="test case";
        $regularExpression = "~^[a-z]+$~";

        $RegexConstraint = new RegexConstraint($regularExpression);
        $this->assertFalse($RegexConstraint->matches($compareWith));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\RegexConstraint::matches
     * @expectedException \InvalidArgumentException
     */
    public function testAssertThrowsInvalidArgumentException() {
        $RegexConstraint = new RegexConstraint("~.*~");
        $RegexConstraint->matches(["wrongType"]);
    }

}
