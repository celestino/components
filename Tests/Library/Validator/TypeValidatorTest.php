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

    use Brickoo\Library\Validator\TypeValidator;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * TypeValidatorTest
     *
     * Test suite for the TypeValidator class.
     * @see Brickoo\Library\Storage\Locker
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class TypeValidatorTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Test if validation of the isString method works without flags.
         * @covers Brickoo\Library\Validator\TypeValidator::IsString
         */
        public function testIsString()
        {
            $this->assertTrue(TypeValidator::IsString('john'));
        }

        /**
         * Test if validation of the isString method throws an exception without flag.
         * @covers Brickoo\Library\Validator\TypeValidator::IsString
         * @expectedException InvalidArgumentException
         */
        public function testIsStringArgumentException()
        {
            TypeValidator::IsString('     ');
        }

        /**
         * Test if validation of the isString method works with empty flag and empty value.
         * @covers Brickoo\Library\Validator\TypeValidator::IsString
         */
        public function testIsStringWithoutEmpty()
        {
            $this->assertTrue(TypeValidator::IsString('     ', TypeValidator::FLAG_STRING_CAN_BE_EMPTY));
        }

        /**
         * Test if validation of the isInteger method works without flags and accepts zero.
         * @covers Brickoo\Library\Validator\TypeValidator::IsInteger
         */
        public function testIsInteger()
        {
            $this->assertTrue(TypeValidator::IsInteger(1234));
            $this->assertTrue(TypeValidator::IsInteger(0));
        }

        /**
         * Test if validation of zero values throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::IsInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsIntegerWithoutZero()
        {
            TypeValidator::IsInteger(0, TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);
        }

        /**
         * Test if validation of the isArray method works without flags and different arguments.
         * @covers Brickoo\Library\Validator\TypeValidator::IsArray
         */
        public function testIsArray()
        {
            $this->assertTrue(TypeValidator::IsArray(array('ok')));
        }

        /**
         * Test if validation of the isArray method throws an exception without flag.
         * @covers Brickoo\Library\Validator\TypeValidator::IsArray
         * @expectedException InvalidArgumentException
         */
        public function testIsArrayArgumentException()
        {
            TypeValidator::IsArray(array());
        }

        /**
         * Test if validation of the isArray method works with flags and empty values.
         * @covers Brickoo\Library\Validator\TypeValidator::IsArray
         */
        public function testIsArrayWithoutEmpty()
        {
            $this->assertTrue(TypeValidator::IsArray(array(), TypeValidator::FLAG_ARRAY_CAN_BE_EMPTY));
        }

        /**
         * Test if validation of the arrayContainsString method works.
         * @covers Brickoo\Library\Validator\TypeValidator::ArrayContainsStrings
         */
        public function testArrayContainsStrings()
        {
            $this->assertTrue(TypeValidator::ArrayContainsStrings(array('ok', 'ok')));
        }

        /**
         * Test if validation of the arrayContainsString method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::ArrayContainsStrings
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsStringsArgumentException()
        {
            TypeValidator::ArrayContainsStrings(array('fail', 1));
        }

        /**
         * Test if validation of the arrayContainsString method works.
         * @covers Brickoo\Library\Validator\TypeValidator::ArrayContainsIntegers
         */
        public function testArrayContainsIntegers()
        {
            $this->assertTrue(TypeValidator::ArrayContainsIntegers(array(1, 2, 3)));
        }

        /**
         * Test if validation of the arrayContainsString method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::ArrayContainsIntegers
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsIntegersArgumentException()
        {
            TypeValidator::ArrayContainsIntegers(array(1, 'throws', 'exception'));
        }

        /**
         * Test if validation of the ArrayContainsKeys method works.
         * @covers Brickoo\Library\Validator\TypeValidator::ArrayContainsKeys
         */
        public function testArrayContainsKeys()
        {
            $this->assertTrue
            (
                TypeValidator::ArrayContainsKeys
                (
                    array('name', 'country'),
                    array('name' => 'some name', 'country' => 'some contry')
                )
            );
        }

        /**
         * Test if validation of ArrayContainsKeys throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::ArrayContainsKeys
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsKeysArgumentException()
        {
            TypeValidator::ArrayContainsKeys(array('name'), array('counter' => 0));
        }

        /**
         * Test if validation of the isBoolean method.
         * @covers Brickoo\Library\Validator\TypeValidator::IsBoolean
         */
        public function testIsBoolean()
        {
            $this->assertTrue(TypeValidator::IsBoolean(true));
            $this->assertTrue(TypeValidator::IsBoolean(false));
        }

        /**
         * Test if validation of the isBoolean method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::IsBoolean
         * @expectedException InvalidArgumentException
         */
        public function testIsBooleanException()
        {
            TypeValidator::IsBoolean('fail');
        }

        /**
         * Test if validation of the isFloat method works without flags.
         * @covers Brickoo\Library\Validator\TypeValidator::IsFloat
         */
        public function testIsFloat()
        {
            $this->assertTrue(TypeValidator::IsFloat(1.234));
        }

        /**
         * Test if validation of the isFloat method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::IsFloat
         * @expectedException InvalidArgumentException
         */
        public function testIsFloatException()
        {
            TypeValidator::IsFloat(1);
        }

        /**
         * Test if validation of the isNotEmpty method.
         * @covers Brickoo\Library\Validator\TypeValidator::IsNotEmpty
         */
        public function testIsNotEmpty()
        {
            $this->assertTrue(TypeValidator::IsNotEmpty(true));
            $this->assertTrue(TypeValidator::IsNotEmpty(1));
            $this->assertTrue(TypeValidator::IsNotEmpty('john'));
            $this->assertTrue(TypeValidator::IsNotEmpty(array('john')));
        }

        /**
         * Test if validation of the isNotEmpty method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::IsNotEmpty
         * @expectedException InvalidArgumentException
         */
        public function testIsNotEmptyException()
        {
            TypeValidator::IsNotEmpty(false);
        }

        /**
         * Test if validation of the isStringOrInteger method.
         * @covers Brickoo\Library\Validator\TypeValidator::IsStringOrInteger
         */
        public function testIsStringOrInteger()
        {
            $this->assertTrue(TypeValidator::IsStringOrInteger('john', TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO));
            $this->assertTrue(TypeValidator::IsStringOrInteger(1, TypeValidator::FLAG_STRING_CAN_BE_EMPTY));
            $this->assertTrue(TypeValidator::IsStringOrInteger(0));
            $this->assertTrue
            (
                TypeValidator::IsStringOrInteger
                (
                    '',
                    TypeValidator::FLAG_STRING_CAN_BE_EMPTY + TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO
                )
            );
        }

        /**
         * Test if validation of the isStringOrInteger method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::IsStringOrInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsStringOrIntegerWithEmptyException()
        {
            TypeValidator::IsStringOrInteger('0');
        }

        /**
         * Test if validation of the isStringOrInteger method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::IsStringOrInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsStringOrIntegerWithoutZeroException()
        {
            TypeValidator::IsStringOrInteger(0, TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);
        }

        /**
         * Test if validation of the isRegex method method.
         * @covers Brickoo\Library\Validator\TypeValidator::MatchesRegex
         */
        public function testMatchesRegex()
        {
            $this->assertTrue(TypeValidator::MatchesRegex('~^[a-z0-9]{4}$~', 'ya12'));
            $this->assertTrue(TypeValidator::MatchesRegex('~^[a-z0-9]{1}$~', 'ya12', TypeValidator::FLAG_REGEX_NEGATIVE_CHECK));
        }

        /**
         * Test if validation of the useRegex method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::MatchesRegex
         * @expectedException InvalidArgumentException
         */
        public function testMatchesRegexException()
        {
            TypeValidator::MatchesRegex('~^[0-9]+$~', 'someValue');
        }

    }

?>