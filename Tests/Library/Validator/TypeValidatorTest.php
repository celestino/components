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
     * Test case for the TypeValidator class.
     * @see Brickoo\Library\Storage\Locker
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id: TypeValidatorTest.php 16 2011-12-23 22:39:50Z celestino $
     */

    class TypeValidatorTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Test if validation works if the arguments are expected.
         * @covers Brickoo\Library\Validator\TypeValidator::Reset
         * @covers Brickoo\Library\Validator\TypeValidator::Validate
         * @covers Brickoo\Library\Validator\TypeValidator::CheckValidation
         * @covers Brickoo\Library\Validator\TypeValidator::isString
         * @covers Brickoo\Library\Validator\TypeValidator::isArray
         */
        public function testValidate()
        {
            TypeValidator::Reset();
            $this->assertTrue(TypeValidator::Validate('isString', array('john', 'mike', 'george')));
            $this->assertTrue(TypeValidator::Validate('isArray', array(array('john', 'mike', 'george'))));
        }

        /**
         * Test if one not valid argument throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::CheckValidation
         * @expectedException InvalidArgumentException
         */
        public function testCheckValidation()
        {
            $this->assertTrue(TypeValidator::Validate('isString', array(1)));
        }

        /**
         * Test if a call with wrong falg type throws an InvalidArgumentException.
         * @covers Brickoo\Library\Validator\TypeValidator::Validate
         * @covers Brickoo\Library\Validator\TypeValidator::isFlagSupported
         * @expectedException InvalidArgumentException
         */
        public function testValidateArgumentsException()
        {
            TypeValidator::Validate('isString', array('john'), array('wrongType'));
        }

        /**
         * Test if a not available validation method throws an BadMethodCallException.
         * @covers Brickoo\Library\Validator\TypeValidator::Validate
         * @covers Brickoo\Library\Validator\TypeValidator::isMethodAvailable
         * @expectedException BadMethodCallException
         */
        public function testValidateMethodException()
        {
            TypeValidator::Validate('isObject', array(new stdClass()));
        }

        /**
         * Test if a not success validation throws an InvalidArgumentException.
         * @covers Brickoo\Library\Validator\TypeValidator::Validate
         * @covers Brickoo\Library\Validator\TypeValidator::ThrowInvalidException
         * @expectedException InvalidArgumentException
         */
        public function testValidateException()
        {
            TypeValidator::Validate('isString', array(1));
        }

        /**
         * Test if validation of the isString method works without flags and different arguments.
         * @covers Brickoo\Library\Validator\TypeValidator::isString
         */
        public function testIsString()
        {
            $this->assertTrue(TypeValidator::Validate('isString', array('john')));
            $this->assertTrue(TypeValidator::Validate('isString', array('john', 'mike', 'george')));
        }

        /**
         * Test if validation of the isString method throws an exception without flag.
         * @covers Brickoo\Library\Validator\TypeValidator::isString
         * @expectedException InvalidArgumentException
         */
        public function testIsStringArgumentException()
        {
            TypeValidator::Validate('isString', array(' '), TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);
        }

        /**
         * Test if validation of the isString method works with flags and empty values.
         * @covers Brickoo\Library\Validator\TypeValidator::isString
         */
        public function testIsStringWithoutEmpty()
        {
            $this->assertTrue
            (
                TypeValidator::Validate
                (
                    'isString',
                    array(' '),
                    TypeValidator::FLAG_STRING_CAN_BE_EMPTY
                )
            );

            $this->assertTrue
            (
                TypeValidator::Validate
                (
                    'isString',
                    array('john', ' ', 'george'),
                    TypeValidator::FLAG_STRING_CAN_BE_EMPTY
                 )
           );
        }

        /**
         * Test if validation of the isInteger method works without flags and accepts zero.
         * @covers Brickoo\Library\Validator\TypeValidator::isInteger
         */
        public function testIsInteger()
        {
            $this->assertTrue(TypeValidator::Validate('isInteger', array(1234)));
            $this->assertTrue(TypeValidator::Validate('isInteger', array(1, 2, 3)));
            $this->assertTrue(TypeValidator::Validate('isInteger', array(0)));
        }

        /**
         * Test if validation of the isInteger method throws an exception with not zero flag.
         * @covers Brickoo\Library\Validator\TypeValidator::isInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsIntegerArgumentException()
        {
            TypeValidator::Validate('isInteger', array(0), TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);
        }

        /**
         * Test if validation of zero values throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::isInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsIntegerWithoutZero()
        {
            TypeValidator::Validate('isInteger', array(0), TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);
        }

        /**
         * Test if validation of the isArray method works without flags and different arguments.
         * @covers Brickoo\Library\Validator\TypeValidator::isArray
         */
        public function testIsArray()
        {
            $this->assertTrue(TypeValidator::Validate('isArray', array(array('ok'))));
        }

        /**
         * Test if validation of the isArray method throws an exception without flag.
         * @covers Brickoo\Library\Validator\TypeValidator::isArray
         * @expectedException InvalidArgumentException
         */
        public function testIsArrayArgumentException()
        {
            TypeValidator::Validate('isArray', array(array()));
        }

        /**
         * Test if validation of the isArray method works with flags and empty values.
         * @covers Brickoo\Library\Validator\TypeValidator::isArray
         */
        public function testIsArrayWithoutEmpty()
        {
            $this->assertTrue
            (
                TypeValidator::Validate
                (
                    'isArray',
                    array(array()),
                    TypeValidator::FLAG_ARRAY_CAN_BE_EMPTY
                )
            );

            $this->assertTrue
            (
                TypeValidator::Validate
                (
                    'isArray',
                    array(array(1), array()),
                    TypeValidator::FLAG_ARRAY_CAN_BE_EMPTY
                 )
           );
        }

        /**
         * Test if validation of the arrayContainsString method works.
         * @covers Brickoo\Library\Validator\TypeValidator::arrayContainsStrings
         */
        public function testArrayContainsStrings()
        {
            $this->assertTrue(TypeValidator::Validate('arrayContainsStrings', array(array('ok', 'ok'))));
        }

        /**
         * Test if validation of the arrayContainsString method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::arrayContainsStrings
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsStringsArgumentException()
        {
            TypeValidator::Validate('arrayContainsStrings', array(array('fail', 1, 2, 3)));
        }

        /**
         * Test if validation of the arrayContainsString method works.
         * @covers Brickoo\Library\Validator\TypeValidator::arrayContainsIntegers
         */
        public function testArrayContainsIntegers()
        {
            $this->assertTrue(TypeValidator::Validate('arrayContainsIntegers', array(array(1, 2, 3))));
        }

        /**
         * Test if validation of the arrayContainsString method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::arrayContainsIntegers
         * @expectedException InvalidArgumentException
         */
        public function testArrayContainsIntegersArgumentException()
        {
            TypeValidator::Validate('arrayContainsIntegers', array(array(1, 'throws', 'exception')));
        }

        /**
         * Test if validation of the isBoolean method.
         * @covers Brickoo\Library\Validator\TypeValidator::isBoolean
         */
        public function testIsBoolean()
        {
            $this->assertTrue(TypeValidator::Validate('isBoolean', array(true)));
            $this->assertTrue(TypeValidator::Validate('isBoolean', array(false)));
        }

        /**
         * Test if validation of the isBoolean method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::isArray
         * @expectedException InvalidArgumentException
         */
        public function testIsBooleanException()
        {
            TypeValidator::Validate('isBoolean', array(1));
        }

        /**
         * Test if validation of the isFloat method works without flags.
         * @covers Brickoo\Library\Validator\TypeValidator::isFloat
         */
        public function testIsFloat()
        {
            $this->assertTrue(TypeValidator::Validate('isFloat', array(1.234)));
        }

        /**
         * Test if validation of the isFloat method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::isFloat
         * @expectedException InvalidArgumentException
         */
        public function testIsFloatException()
        {
            TypeValidator::Validate('isFloat', array(1));
        }

        /**
         * Test if validation of the isNotEmpty method.
         * @covers Brickoo\Library\Validator\TypeValidator::isNotEmpty
         */
        public function testIsNotEmpty()
        {
            $this->assertTrue(TypeValidator::Validate('isNotEmpty', array(true)));
            $this->assertTrue(TypeValidator::Validate('isNotEmpty', array(1)));
            $this->assertTrue(TypeValidator::Validate('isNotEmpty', array('john')));
            $this->assertTrue(TypeValidator::Validate('isNotEmpty', array(array('john'))));
        }

        /**
         * Test if validation of the isNotEmpty method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::isNotEmpty
         * @expectedException InvalidArgumentException
         */
        public function testIsNotEmptyException()
        {
            TypeValidator::Validate('isNotEmpty', array(false));
        }

        /**
         * Test if validation of the isStringOrInteger method.
         * @covers Brickoo\Library\Validator\TypeValidator::isStringOrInteger
         */
        public function testIsStringOrInteger()
        {
            $this->assertTrue(TypeValidator::Validate('isStringOrInteger', array('john'), TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO));
            $this->assertTrue(TypeValidator::Validate('isStringOrInteger', array(1), TypeValidator::FLAG_STRING_CAN_BE_EMPTY));
            $this->assertTrue(TypeValidator::Validate('isStringOrInteger', array('john', 1)));
            $this->assertTrue(TypeValidator::Validate('isStringOrInteger', array(0)));
            $this->assertTrue
            (
                TypeValidator::Validate
                (
                    'isStringOrInteger',
                    array('', 1),
                    TypeValidator::FLAG_STRING_CAN_BE_EMPTY + TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO
                )
            );
        }

        /**
         * Test if validation of the isStringOrInteger method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::isStringOrInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsStringOrIntegerWithEmptyException()
        {
            TypeValidator::Validate('isStringOrInteger', array('0'));
        }

        /**
         * Test if validation of the isStringOrInteger method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::isStringOrInteger
         * @expectedException InvalidArgumentException
         */
        public function testIsStringOrIntegerWithoutZeroException()
        {
            TypeValidator::Validate('isStringOrInteger', array(0), TypeValidator::FLAG_INTEGER_CAN_NOT_BE_ZERO);
        }

        /**
         * Test if validation of the isRegex method method.
         * @covers Brickoo\Library\Validator\TypeValidator::useRegex
         */
        public function testUseRegex()
        {
            $this->assertTrue(TypeValidator::Validate('useRegex', array(array('~^[a-z0-9]{4}$~', 'ya12'))));
            $this->assertTrue
            (
                TypeValidator::Validate
                (
                    'useRegex',
                    array(array('~^[a-z0-9]{1}$~', 'ya12')),
                    TypeValidator::FLAG_REGEX_NEGATIVE_CHECK
                )
            );
        }

        /**
         * Test if validation of the useRegex method throws an exception.
         * @covers Brickoo\Library\Validator\TypeValidator::useRegex
         * @expectedException InvalidArgumentException
         */
        public function testUseRegexException()
        {
            TypeValidator::Validate('useRegex', array('wrongType'));
        }

    }

    /**
     * Class need to reset the static Validator assigned.
     */
    class TypeValidatorFixture extends TypeValidator
    {
        /**
         * Resets the static validator assigned.
         * @return void
         */
        public static function Reset()
        {
            static::$Validator = null;
        }
    }

?>