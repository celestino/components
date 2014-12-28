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
        $this->assertInstanceOf("\\Brickoo\\Component\\Validation\\Constraint\\Constraint", $containsKeysConstraint);
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
