<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Validator\Constraint\TraversableContainsCharacters;

    require_once "Fixture/TraversableFixture.php";

    /**
     * TraversableContainsCharactersTest
     *
     * Test suite for the TraversableContainsCharacters class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class TraversableContainsCharactersTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsCharacters::__construct
         */
        public function testConstructor() {
            $cTypeFunction = "alpha";

            $TraversableContainsCharacters = new TraversableContainsCharacters($cTypeFunction);
            $this->assertInstanceOf('Brickoo\Validator\Constraint\Interfaces\Constraint', $TraversableContainsCharacters);
            $this->assertAttributeEquals("ctype_". $cTypeFunction, 'cTypeFunctionName', $TraversableContainsCharacters);
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsCharacters::__construct
         * @expectedException InvalidArgumentException
         */
        public function testContructorTypeThrowsInvalidArgumentException() {
            new TraversableContainsCharacters("cTypeDoesNotExist");
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsCharacters::assert
         */
        public function testAssertionOfTraversableValues() {
            $TraversableContainsCharacters = new TraversableContainsCharacters("alnum");
            $this->assertTrue($TraversableContainsCharacters->assert(new Fixture\TraversableFixture()));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsCharacters::assert
         */
        public function testAssertionOfArrayValues() {
            $TraversableContainsCharacters = new TraversableContainsCharacters("alpha");
            $this->assertTrue($TraversableContainsCharacters->assert(array("test", "case")));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsCharacters::assert
         */
        public function testAssertionOfOneValueFails() {
            $TraversableContainsCharacters = new TraversableContainsCharacters("alpha");
            $this->assertFalse($TraversableContainsCharacters->assert(array("test", "!!!")));
        }

        /**
         * @covers Brickoo\Validator\Constraint\TraversableContainsCharacters::assert
         * @expectedException InvalidArgumentException
         */
        public function testAssertionThrowsInvalidArgumentException() {
            $TraversableContainsCharacters = new TraversableContainsCharacters("alpha");
            $TraversableContainsCharacters->assert("wrongType");
        }

    }