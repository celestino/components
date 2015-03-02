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

namespace Brickoo\Tests\Component\Validation\Validator;

use Brickoo\Component\Validation\Constraint\IsEqualToConstraint;
use Brickoo\Component\Validation\Constraint\IsInternalTypeConstraint;
use Brickoo\Component\Validation\Validator\ConstraintValidator;
use PHPUnit_Framework_TestCase;

/**
 * ConstraintValidator
 *
 * Test suite for the ConstraintValidator class.
 * @see Brickoo\Component\Validation\Validator\ConstraintValidator
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ConstraintValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Validation\Validator\ConstraintValidator::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsInvalidArgumentsException() {
        new ConstraintValidator(
            new IsEqualToConstraint("12345"),
            ["wrongType"]
        );
    }

    /**
     * @covers Brickoo\Component\Validation\Validator\ConstraintValidator::__construct
     * @covers Brickoo\Component\Validation\Validator\ConstraintValidator::isValid
     */
    public function testMatchingValueWithConstraints() {
        $constraintValidator = new ConstraintValidator(
            new IsEqualToConstraint("12345"),
            new IsInternalTypeConstraint("string")
        );
        $this->assertTrue($constraintValidator->isValid("12345"));
        $this->assertFalse($constraintValidator->isValid(12345));
    }

    /**
     * @covers Brickoo\Component\Validation\Validator\ConstraintValidator::isValid
     * @covers Brickoo\Component\Validation\Validator\ConstraintValidator::getFailedConstraint
     */
    public function testGetFailedConstraint() {
        $constraintValidator = new ConstraintValidator(
            new IsEqualToConstraint("12345"),
            ($constraint = new IsInternalTypeConstraint("string"))
        );
        $this->assertFalse($constraintValidator->isValid(12345));
        $this->assertSame($constraint, $constraintValidator->getFailedConstraint());
    }

}
