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

namespace Brickoo\Tests\Component\Validation\Validator;

use Brickoo\Component\Validation\Constraint\IsEqualToConstraint,
    Brickoo\Component\Validation\Constraint\IsTypeConstraint,
    Brickoo\Component\Validation\Validator\ConstraintValidator,
    PHPUnit_Framework_TestCase;

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
            new IsTypeConstraint("string")
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
            ($constraint = new IsTypeConstraint("string"))
        );
        $this->assertFalse($constraintValidator->isValid(12345));
        $this->assertSame($constraint, $constraintValidator->getFailedConstraint());
    }

}