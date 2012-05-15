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

    use Brickoo\Validator\TypeValidator;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * TypeValidatorTest
     *
     * Test suite for the TypeValidator class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class TypeValidatorTest extends PHPUnit_Framework_TestCase {

        /**
        * Test if validation of the isString.
        * @covers Brickoo\Validator\TypeValidator::IsString
        */
        public function testIsString() {
            $this->assertTrue(TypeValidator::IsString('john'));
        }

        /**
         * Test if validation of the isString method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsString
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsStringArgumentException() {
            TypeValidator::IsString(new stdClass());
        }

        /**
         * Test if validation of the testIsStringAndNotEmpty.
         * @covers Brickoo\Validator\TypeValidator::IsStringAndNotEmpty
         */
        public function testIsStringAndNotEmpty() {
            $this->assertTrue(TypeValidator::IsStringAndNotEmpty('john'));
        }

        /**
         * Test if validation of the testIsStringAndNotEmpty method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsStringAndNotEmpty
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsStringAndNotEmptyArgumentException() {
            TypeValidator::IsStringAndNotEmpty('     ');
        }

        /**
         * Test if validation of the isInteger method works and accepts zero.
         * @covers Brickoo\Validator\TypeValidator::IsInteger
         */
        public function testIsInteger() {
            $this->assertTrue(TypeValidator::IsInteger(1234));
            $this->assertTrue(TypeValidator::IsInteger(0));
        }

        /**
         * Test if validation of the IsInteger method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsInteger
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsIntegerArgumentException() {
            TypeValidator::IsInteger(new stdClass());
        }

        /**
         * Test if validation of the IsIntegerAndNotZero method works.
         * @covers Brickoo\Validator\TypeValidator::IsIntegerAndNotZero
         */
        public function testIsIntegerAndNotZero() {
            $this->assertTrue(TypeValidator::IsIntegerAndNotZero(1234));
        }

        /**
         * Test if validation of zero values works.
         * @covers Brickoo\Validator\TypeValidator::IsIntegerAndNotZero
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsIntegerAndNotZeroException() {
            TypeValidator::IsIntegerAndNotZero(0);
        }

        /**
         * Test if validation of the isStringOrInteger method works.
         * @covers Brickoo\Validator\TypeValidator::IsStringOrInteger
         */
        public function testIsStringOrInteger() {
            $this->assertTrue(TypeValidator::IsStringOrInteger(''));
            $this->assertTrue(TypeValidator::IsStringOrInteger(0));
        }

        /**
         * Test if validation of the isStringOrInteger method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsStringOrInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsStringOrIntegerException() {
            $this->assertTrue(TypeValidator::IsStringOrInteger(array('wrongType')));
        }

        /**
         * Test if validation of an object works.
         * @covers Brickoo\Validator\TypeValidator::IsObject
         */
        public function testIsObject() {
            $this->assertTrue(TypeValidator::IsObject(new stdClass()));
        }

        /**
         * Test if validation of a non object throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsObject
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsObjectArgumentException() {
            TypeValidator::IsObject('wrongType');
        }

        /**
         * Test if validation of the isArray method workss and different arguments.
         * @covers Brickoo\Validator\TypeValidator::IsArrayAndNotEmpty
         */
        public function testIsArray() {
            $this->assertTrue(TypeValidator::IsArrayAndNotEmpty(array('ok')));
        }

        /**
         * Test if validation of the isArray method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsArrayAndNotEmpty
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsArrayArgumentException() {
            TypeValidator::IsArrayAndNotEmpty(array());
        }

        /**
         * Test if validation of the arrayContainsString method works.
         * @covers Brickoo\Validator\TypeValidator::ArrayContainsStrings
         */
        public function testArrayContainsStrings() {
            $this->assertTrue(TypeValidator::ArrayContainsStrings(array('ok', 'ok')));
        }

        /**
         * Test if validation of the arrayContainsString method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::ArrayContainsStrings
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsStringsArgumentException() {
            TypeValidator::ArrayContainsStrings(array('fail', 1));
        }

        /**
         * Test if validation of the arrayContainsString method works.
         * @covers Brickoo\Validator\TypeValidator::ArrayContainsIntegers
         */
        public function testArrayContainsIntegers() {
            $this->assertTrue(TypeValidator::ArrayContainsIntegers(array(1, 2, 3)));
        }

        /**
         * Test if validation of the arrayContainsString method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::ArrayContainsIntegers
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsIntegersArgumentException() {
            TypeValidator::ArrayContainsIntegers(array(1, 'throws', 'exception'));
        }

        /**
         * Test if validation of the ArrayContainsKeys method works.
         * @covers Brickoo\Validator\TypeValidator::ArrayContainsKeys
         */
        public function testArrayContainsKeys() {
            $this->assertTrue(
                TypeValidator::ArrayContainsKeys (
                    array('name', 'country'),
                    array('name' => 'some name', 'country' => 'some contry')
                )
            );
        }

        /**
         * Test if validation of ArrayContainsKeys throws an exception.
         * @covers Brickoo\Validator\TypeValidator::ArrayContainsKeys
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsKeysArgumentException() {
            TypeValidator::ArrayContainsKeys(array('name'), array('counter' => 0));
        }

        /**
         * Test if validation of the isBoolean method works.
         * @covers Brickoo\Validator\TypeValidator::IsBoolean
         */
        public function testIsBoolean() {
            $this->assertTrue(TypeValidator::IsBoolean(true));
            $this->assertTrue(TypeValidator::IsBoolean(false));
        }

        /**
         * Test if validation of the isBoolean method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsBoolean
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsBooleanException() {
            TypeValidator::IsBoolean(null);
        }

        /**
         * Test if validation of the isFloat method works.
         * @covers Brickoo\Validator\TypeValidator::IsFloat
         */
        public function testIsFloat() {
            $this->assertTrue(TypeValidator::IsFloat(1.234));
        }

        /**
         * Test if validation of the isFloat method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsFloat
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsFloatException() {
            TypeValidator::IsFloat(1);
        }

        /**
         * Test if validation of the isNotEmpty method works.
         * @covers Brickoo\Validator\TypeValidator::IsNotEmpty
         */
        public function testIsNotEmpty() {
            $this->assertTrue(TypeValidator::IsNotEmpty(true));
            $this->assertTrue(TypeValidator::IsNotEmpty(1));
            $this->assertTrue(TypeValidator::IsNotEmpty('john'));
            $this->assertTrue(TypeValidator::IsNotEmpty(array('john')));
        }

        /**
         * Test if validation of the isNotEmpty method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsNotEmpty
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsNotEmptyException() {
            TypeValidator::IsNotEmpty(false);
        }

        /**
         * Test if validation of the isCallable method works.
         * @covers Brickoo\Validator\TypeValidator::IsCallable
         */
        public function testIsCallable() {
            $this->assertTrue(TypeValidator::IsCallable(array($this, 'testIsCallable')));
        }

        /**
         * Test if validation of the isCallable method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::IsCallable
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testIsCallableException() {
            TypeValidator::IsCallable(array('wrontType'));
        }

        /**
         * Test if validation of the isRegex method works.
         * @covers Brickoo\Validator\TypeValidator::MatchesRegex
         */
        public function testMatchesRegex() {
            $this->assertTrue(TypeValidator::MatchesRegex('~^[a-z0-9]{4}$~', 'ya12'));
        }

        /**
         * Test if validation of the useRegex method throws an exception.
         * @covers Brickoo\Validator\TypeValidator::MatchesRegex
         * @covers Brickoo\Validator\TypeValidator::ThrowInvalidArgumentException
         * @expectedException InvalidArgumentException
         */
        public function testMatchesRegexException() {
            TypeValidator::MatchesRegex('~^[0-9]+$~', 'someValue');
        }

    }