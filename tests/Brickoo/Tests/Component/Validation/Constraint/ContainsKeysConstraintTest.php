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

use Brickoo\Component\Validation\Constraint\ContainsKeysConstraint;

require_once "Fixture/TraversableFixture.php";

/**
 * ContainsKeysConstraintTest
 *
 * Test suite for the ContainsKeysConstraint class.
 * @see Brickoo\Component\Validation\Constraint\ContainsKeysConstraint
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ContainsKeysConstraintTest extends \PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::__construct */
    public function testConstructor() {
        $expectedKeys = array("key1", "key2", "key3");
        $containsKeysConstraint = new ContainsKeysConstraint($expectedKeys);
        $this->assertInstanceOf("\\Brickoo\\Component\\Validation\\Constraint", $containsKeysConstraint);
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::getTraversableKeys
     */
    public function testAssertionOfValuesWithTraversableObject() {
        $compareFrom = new Fixture\TraversableFixture();
        $expectedKeys = array("key1", "key2", "key3");

        $containsKeysConstraint = new ContainsKeysConstraint($expectedKeys);
        $this->assertTrue($containsKeysConstraint->matches($compareFrom));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::getTraversableKeys
     */
    public function testAssertionOfValuesWithArray() {
        $compareFrom = array("key1" => "unit", "key2" => "test", "key3" => "works");
        $expectedKeys = array("key1", "key2");

        $containsKeysConstraint = new ContainsKeysConstraint($expectedKeys);
        $this->assertTrue($containsKeysConstraint->matches($compareFrom));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::matches
     * @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::getTraversableKeys
     */
    public function testAssertionOfOneValueFailure() {
        $compareFrom = array("key1" => "unit", "key2" => "test");
        $expectedKeys = array("key100", "key200");

        $containsKeysConstraint = new ContainsKeysConstraint($expectedKeys);
        $this->assertFalse($containsKeysConstraint->matches($compareFrom));
    }

    /**
     * @covers Brickoo\Component\Validation\Constraint\ContainsKeysConstraint::matches
     * @expectedException \InvalidArgumentException
     */
    public function testAssertionThrowsInvalidArgumentException() {
        $containsKeysConstraint = new ContainsKeysConstraint(array("key"));
        $containsKeysConstraint->matches("wrongType");
    }

}
