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

use Brickoo\Component\Validation\Constraint\ContainsCharactersOfTypeConstraint;
use PHPUnit_Framework_TestCase;

/**
 * ContainsCharactersOfTypeConstraintTest
 *
 * Test suite for the ContainsCharactersOfTypeConstraint class.
 * @see Brickoo\Component\Validation\Constraint\ContainsCharactersOfTypeConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ContainsCharactersOfTypeConstraintTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Validation\Constraint\ContainsCharactersOfTypeConstraint::__construct */
    public function testConstructor() {
        $cTypeFunction = "alpha";
        $ContainsCharactersOfTypeConstraint = new ContainsCharactersOfTypeConstraint($cTypeFunction);
        $this->assertInstanceOf("\\Brickoo\\Component\\Validation\\Constraint\\Constraint", $ContainsCharactersOfTypeConstraint);
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\ContainsCharactersOfTypeConstraint::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorTypeThrowsInvalidArgumentException() {
        new ContainsCharactersOfTypeConstraint("cTypeDoesNotExist");
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\FunctionCallbackConstraint::__construct
     * @covers Brickoo\Component\Validation\Constraint\FunctionCallbackConstraint::matches
     */
    public function testAssertionOfAnValidValue() {
        $ContainsCharactersOfTypeConstraint = new ContainsCharactersOfTypeConstraint("alpha");
        $this->assertTrue($ContainsCharactersOfTypeConstraint->matches("test"));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\FunctionCallbackConstraint::__construct
     * @covers Brickoo\Component\Validation\Constraint\FunctionCallbackConstraint::matches
     */
    public function testAssertionOfOneValueFails() {
        $ContainsCharactersOfTypeConstraint = new ContainsCharactersOfTypeConstraint("alpha");
        $this->assertFalse($ContainsCharactersOfTypeConstraint->matches("failure !!!"));
    }

}
