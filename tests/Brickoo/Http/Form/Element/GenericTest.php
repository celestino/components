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

    namespace Tests\Brickoo\Http\Form\Element;

    use Brickoo\Http\Form\Element\Generic;

    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Generic class.
     * @see Brickoo\Http\Form\Element\Generic
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class GenericTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Generic class.
         * @var \Brickoo\Http\Form\Element\Generic
         */
        protected $Generic;

        /**
         * Sets up the Generic instance which is used by some tests.
         * @return void
         */
        protected function setUp() {
            $this->Generic = new Generic('name', 'BrickOO', false);
        }

        /**
         * Test if the constructor is correctly initialized and the instance properties are set.
         * @covers Brickoo\Http\Form\Element\Generic::__construct
         */
        public function testConstructor() {
            $name            = 'name';
            $defaultValue    = 'BrickOO';
            $required        = true;

            $this->assertInstanceOf(
                'Brickoo\Http\Form\Element\Interfaces\Element',
                ($Element = new Generic($name, $defaultValue, $required))
            );
            $this->assertAttributeEquals($name, 'name', $Element);
            $this->assertAttributeEquals($defaultValue, 'value', $Element);
            $this->assertAttributeEquals($required, 'required', $Element);
        }

        /**
         * Test if the element name can be retrieved.
         * @covers Brickoo\Http\Form\Element\Generic::getName
         */
        public function testGetName() {
            $this->assertEquals('name', $this->Generic->getName());
        }

        /**
         * Test if the element value can be retrieved.
         * @covers Brickoo\Http\Form\Element\Generic::getValue
         */
        public function testGetValue() {
            $this->assertEquals('BrickOO', $this->Generic->getValue());
        }

        /**
         * Test if the value is recognized as available .
         * @covers Brickoo\Http\Form\Element\Generic::hasValue
         */
        public function testHasValue() {
            $this->assertTrue($this->Generic->hasValue());
        }

        /**
         * Test i the requirement of this element field can be changed.
         * @covers Brickoo\Http\Form\Element\Generic::setRequired
         * @covers Brickoo\Http\Form\Element\Generic::isRequired
         */
        public function testSetRequired() {
            $this->assertFalse($this->Generic->isRequired());
            $this->assertSame($this->Generic, $this->Generic->setRequired(true));
            $this->assertTrue($this->Generic->isRequired());
            $this->assertSame($this->Generic, $this->Generic->setRequired(false));
            $this->assertFalse($this->Generic->isRequired());
        }

        /**
         * Test if the error messages can be retrieved.
         * @covers Brickoo\Http\Form\Element\Generic::getErrorMessages
         */
        public function testGetErrorMessages() {
            require_once ('Fixture/GenericFixture.php');
            $errorMessages = array('The name is not valid.');

            $GenericFixture = new Fixture\GenericFixture('name');
            $this->assertNull($GenericFixture->getErrorMessages());
            $GenericFixture->setErrorMessages($errorMessages);
            $this->assertEquals($errorMessages, $GenericFixture->getErrorMessages());
        }

        /**
         * Test if available error messages are recognized.
         * @covers Brickoo\Http\Form\Element\Generic::hasErrorMessages
         */
        public function testHasErrorMessages() {
            require_once ('Fixture/GenericFixture.php');
            $errorMessages = array('msg1');

            $GenericFixture = new Fixture\GenericFixture('name');
            $this->assertFalse($GenericFixture->hasErrorMessages());
            $GenericFixture->setErrorMessages($errorMessages);
            $this->assertTrue($GenericFixture->hasErrorMessages());
        }

        /**
         * Test if a validtor can be added to the element.
         * @covers Brickoo\Http\Form\Element\Generic::addValidator
         */
        public function testAddValidator() {
            $Validator = function($field, $value, $parameters){};
            $errorMessage = 'The name is not valid.';

            $expected = array(array(
                Generic::CALLBACK_FIELD   => $Validator,
                Generic::MESSAGE_FIELD    => $errorMessage
            ));

            $this->assertSame($this->Generic, $this->Generic->addValidator($Validator, $errorMessage));
            $this->assertAttributeEquals($expected, 'validators', $this->Generic);
        }

        /**
         * Test if trying to pass a non callable paramater throws an exception.
         * @covers Brickoo\Http\Form\Element\Generic::addValidator
         * @expectedException \InvalidArgumentException
         */
        public function testAddValidatorArgumentException() {
            $this->Generic->addValidator('wrongType', 'msg');
        }

        /**
         * Test if the value of the element field can be trieved from the request parameters.
         * @covers Brickoo\Http\Form\Element\Generic::getRequestValue
         */
        public function testGetRequestValue() {
            $this->assertEquals('BrickOO', $this->Generic->getRequestValue(array('name' => 'BrickOO')));
        }

        /**
         * Test if a successful validation without requirements and parameter returns boolean true.
         * @covers Brickoo\Http\Form\Element\Generic::isValid
         * @depends testGetRequestValue
         */
        public function testIsValidationWithoutRequirementAndParameters() {
            $this->Generic->setRequired(false);
            $this->assertTrue($this->Generic->isValid(array()));
        }

        /**
         * Test if a validation with requirements and NO parameter returns boolean false.
         * @covers Brickoo\Http\Form\Element\Generic::isValid
         * @depends testGetRequestValue
         */
        public function testIsValidationWithRequirementAndWithoutParameters() {
            $this->Generic->setRequired(true);
            $this->assertFalse($this->Generic->isValid(array()));
        }

        /**
         * Test if a successful validation with a validator, requirements and parameters returns boolean true.
         * This validator uses a simple string assertion.
         * @covers Brickoo\Http\Form\Element\Generic::isValid
         * @depends testGetRequestValue
         */
        public function testIsValidationWithValidatorAndRequirement() {
            $Validator = function($field, $value, $parameters){ return is_string($value);};

            $this->Generic->setRequired(true);
            $this->Generic->addValidator($Validator, 'The name is not valid.');
            $this->assertTrue($this->Generic->isValid(array('name' => 'BrickOO')));
        }

        /**
         * Test if a failed validation with a validator, requirements and parameters returns boolean false.
         * This validator uses a simple integer assertion.
         * @covers Brickoo\Http\Form\Element\Generic::isValid
         * @depends testGetRequestValue
         */
        public function testIsValidationFailureWithValidatorAndRequirement() {
            $Validator = function($field, $value, $parameters){ return is_int($value);};

            $this->Generic->setRequired(true);
            $this->Generic->addValidator($Validator, 'The name is not valid.');
            $this->assertFalse($this->Generic->isValid(array('name' => 'BrickOO')));
        }

        /**
         * Test if the filter is applied after a valid validation to the element value.
         * @covers Brickoo\Http\Form\Element\Generic::filter
         */
        public function testFilter() {
            require_once ('Fixture/GenericFixture.php');

            $Validator = function($field, $value, $parameters){ return is_string($value);};

            $GenericFixture = new Fixture\GenericFixture('name');
            $GenericFixture->setRequired(true);
            $GenericFixture->addValidator($Validator, 'The name is not valid.');
            $this->assertTrue($GenericFixture->isValid(array('name' => 'BrickOO')));
            $this->assertAttributeEquals('BRICKOO', 'value', $GenericFixture);
        }

    }