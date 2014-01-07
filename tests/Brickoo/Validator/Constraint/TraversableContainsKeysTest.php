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

    namespace Tests\Brickoo\Validator\Constraint;

    use Brickoo\Validator\Constraint\TraversableContainsKeys;

    require_once "Fixture/TraversableFixture.php";

    /**
     * TraversableContainsKeysTest
     *
     * Test suite for the ssertContainsKeys class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class TraversableContainsKeysTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::__construct
         */
        public function testConstructor() {
            $expectedKeys = array("key1", "key2", "key3");

            $TraversableContainsKeys = new TraversableContainsKeys($expectedKeys);
            $this->assertInstanceOf('Brickoo\Validator\Constraint\Interfaces\Constraint', $TraversableContainsKeys);
            $this->assertAttributeEquals($expectedKeys, 'expectedKeys', $TraversableContainsKeys);
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::assert
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::getTraversableKeys
         */
        public function testAssertionOfValuesWithTraversableObject() {
            $compareFrom = new Fixture\TraversableFixture();
            $expectedKeys = array("key1", "key2", "key3");

            $TraversableContainsKeys = new TraversableContainsKeys($expectedKeys);
            $this->assertTrue($TraversableContainsKeys->assert($compareFrom));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::assert
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::getTraversableKeys
         */
        public function testAssertionOfValuesWithArray() {
            $compareFrom = array("key1" => "unit", "key2" => "test", "key3" => "works");
            $expectedKeys = array("key1", "key2");

            $TraversableContainsKeys = new TraversableContainsKeys($expectedKeys);
            $this->assertTrue($TraversableContainsKeys->assert($compareFrom));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::assert
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::getTraversableKeys
         */
        public function testAssertionOfOneValueFailure() {
            $compareFrom = array("key1" => "unit", "key2" => "test");
            $expectedKeys = array("key100", "key200");

            $TraversableContainsKeys = new TraversableContainsKeys($expectedKeys);
            $this->assertFalse($TraversableContainsKeys->assert($compareFrom));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsKeys::assert
         * @expectedException InvalidArgumentException
         */
        public function testAssertionThrowsInvalidArgumentException() {
            $TraversableContainsKeys = new TraversableContainsKeys(array("key"));
            $TraversableContainsKeys->assert("wrongType");
        }

    }