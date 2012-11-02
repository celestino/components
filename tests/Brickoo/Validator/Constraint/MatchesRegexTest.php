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

    use Brickoo\Validator\Constraint\MatchesRegex;

    /**
     * MatchesRegexTest
     *
     * Test suite for the MatchesRegex class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class MatchesRegexTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Validator\Constraint\MatchesRegex::__construct
         */
        public function testConstructor() {
            $regularExpression = "~^[a-z\s]+$~";

            $MatchesRegex = new MatchesRegex($regularExpression);
            $this->assertInstanceOf('Brickoo\Validator\Constraint\Interfaces\Constraint', $MatchesRegex);
            $this->assertAttributeEquals($regularExpression, 'regularExpression', $MatchesRegex);
        }

        /**
         * @covers Brickoo\Validator\Constraint\MatchesRegex::__construct
         * @expectedException InvalidArgumentException
         */
        public function testConstructorThrowsInvalidArgumentException() {
            new MatchesRegex(array("wrongType"));
        }

        /**
         * @covers Brickoo\Validator\Constraint\MatchesRegex::assert
         */
        public function testAssertRegularExpression() {
            $compareWith ="test case";
            $regularExpression = "~^[a-z\s]+$~";

            $MatchesRegex = new MatchesRegex($regularExpression);
            $this->assertTrue($MatchesRegex->assert($compareWith));
        }

        /**
         * @covers Brickoo\Validator\Constraint\MatchesRegex::assert
         */
        public function testAssertRegularExpressionFails() {
            $compareWith ="test case";
            $regularExpression = "~^[a-z]+$~";

            $MatchesRegex = new MatchesRegex($regularExpression);
            $this->assertFalse($MatchesRegex->assert($compareWith));
        }

        /**
         * @covers Brickoo\Validator\Constraint\MatchesRegex::assert
         * @expectedException InvalidArgumentException
         */
        public function testAssertThrowsInvalidArgumentException() {
            $MatchesRegex = new MatchesRegex("~.*~");
            $MatchesRegex->assert(array("wrongType"));
        }

    }