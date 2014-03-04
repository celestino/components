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

use Brickoo\Component\Validation\Constraint\LengthConstraint,
    PHPUnit_Framework_TestCase;

/**
 * LengthConstraint
 *
 * Test suite for the LengthConstraint class.
 * @see Brickoo\Component\Validation\LengthConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LengthConstraintTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Validation\Constraint\LengthConstraint::__construct
     * @covers Brickoo\Component\Validation\Constraint\LengthConstraint::matches
     */
    public function testMatchingValue() {
        $lengthConstraint = new LengthConstraint(3, 5);
        $this->assertTrue($lengthConstraint->matches("1234"));
        $this->assertFalse($lengthConstraint->matches("1"));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\LengthConstraint::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructInvalidMinArgumentThrowsException() {
        new LengthConstraint("wrongType");
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\LengthConstraint::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructInvalidMaxArgumentThrowsException() {
        new LengthConstraint(1, "wrongType");
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\LengthConstraint::matches
     * @expectedException \InvalidArgumentException
     */
    public function testMatchesInvalidValueArgumentThrowsException() {
        $lengthConstraint = new LengthConstraint(1);
        $lengthConstraint->matches(array("wrongType"));
    }

}