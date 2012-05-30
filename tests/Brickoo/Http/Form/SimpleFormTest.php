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

    namespace Tests\Brickoo\Http\Form;

    use Brickoo\Http\Form\SimpleForm;

    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the SimpleForm class.
     * @see Brickoo\Http\Form\SimpleForm
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SimpleFormTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the SimpleForm class.
         * @var \Brickoo\Http\Form\SimpleForm
         */
        protected $SimpleForm;

        /**
         * Sets up the SimpleForm instance which is used by some tests.
         * @return void
         */
        protected function setUp() {
            $this->SimpleForm = new SimpleForm();
        }

        /**
         * Test if the constructor initializes the instance correctly.
         * @covers Brickoo\Http\Form\SimpleForm::__construct
         */
        public function testConstructorWithDependencyInjection() {
           $Container = new \Brickoo\Memory\Container();
           $SimpleForm = new SimpleForm($Container);
           $this->assertAttributeEquals(0, 'errors', $SimpleForm);
           $this->assertAttributeSame($Container, 'Elements', $SimpleForm);
        }


        /**
         * Test if the constructor initializes the instance correctly
         * and the Container dependency is lazy initialized.
         * @covers Brickoo\Http\Form\SimpleForm::__construct
         */
        public function testConstructorWithLazyInitialization() {
            $SimpleForm = new SimpleForm();
            $this->assertAttributeEquals(0, 'errors', $SimpleForm);
            $this->assertAttributeInstanceOf('Brickoo\Memory\Container', 'Elements', $SimpleForm);
        }

        /**
         * Test if validation elements can be added to the form Container dependency
         * and the SimpleForm reference is returned.
         * @covers Brickoo\Http\Form\SimpleForm::addElement
         */
        public function testAddElement() {
            $Element = $this->getMock('Brickoo\Http\Form\Element\Generic', null, array('name'));

            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('set')
                      ->with($this->equalTo('name'), $this->equalTo($Element));
            $Container->expects($this->once())
                      ->method('has')
                      ->will($this->returnValue(false));

            $SimpleForm = new SimpleForm($Container);
            $this->assertSame($SimpleForm, $SimpleForm->addElement($Element));
        }

        /**
         * Test if trying to set an element which name is already set throws an exception.
         * @covers Brickoo\Http\Form\SimpleForm::addElement
         * @expectedException Brickoo\Core\Exceptions\ValueOverwriteException
         */
        public function testAddElementOverwriteException() {
            $Element = $this->getMock('Brickoo\Http\Form\Element\Generic', null, array('name'));
            $this->SimpleForm->addElement($Element);
            $this->SimpleForm->addElement($Element);
        }

        /**
         * Test if the validation elements can be retrieved associated with the elements name
         * from the Container dependency.
         * @covers Brickoo\Http\Form\SimpleForm::getElements
         * @depends testAddElement
         */
        public function testGetElements() {
            $Element1 = $this->getMock('Brickoo\Http\Form\Element\Generic', null, array('name'));
            $Element2 = $this->getMock('Brickoo\Http\Form\Element\Generic', null, array('age'));
            $expected = array('name' => $Element1, 'age' => $Element2);

            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('toArray')
                      ->will($this->returnValue($expected));

            $SimpleForm = new SimpleForm($Container);
            $this->assertEquals($expected, $SimpleForm->getElements());
        }

        /**
         * Test if the recognition of available elements works.
         * @covers Brickoo\Http\Form\SimpleForm::hasElements
         * @depends testAddElement
         */
        public function testHasElements() {
            $this->assertFalse($this->SimpleForm->hasElements());

            $Element = $this->getMock('Brickoo\Http\Form\Element\Generic', null, array('name'));
            $this->SimpleForm->addElement($Element);
            $this->assertTrue($this->SimpleForm->hasElements());
        }

        /**
         * Test if an element can be recognized through his name.
         * @covers Brickoo\Http\Form\SimpleForm::hasElement
         */
        public function testHasElement() {
            $this->assertFalse($this->SimpleForm->hasElement('name'));

            $Element = $this->getMock('Brickoo\Http\Form\Element\Generic', null, array('name'));
            $this->SimpleForm->addElement($Element);
            $this->assertTrue($this->SimpleForm->hasElement('name'));
        }

        /**
         * Test if an element can be removed through his unique name
         * and the SimpleForm reference is retruend.
         * @covers Brickoo\Http\Form\SimpleForm::removeElement
         */
        public function testRemoveElement() {
            $Element = $this->getMock('Brickoo\Http\Form\Element\Generic', null, array('name'));
            $this->SimpleForm->addElement($Element);
            $this->assertSame($this->SimpleForm, $this->SimpleForm->removeElement('name'));
            $this->assertEquals(array(), $this->SimpleForm->getElements());
        }

        /**
         * Test if trying to remove an element which is not available throws an exception.
         * @covers Brickoo\Http\Form\SimpleForm::removeElement
         * @covers Brickoo\Http\Form\Exceptions\ElementNotAvailableException
         * @expectedException Brickoo\Http\Form\Exceptions\ElementNotAvailableException
         */
        public function testRemoveElementNotAvailableException() {
            $this->SimpleForm->removeElement('name');
        }

        /**
         * Test if the amount of errors is returned.
         * @covers Brickoo\Http\Form\SimpleForm::getErrors
         */
        public function testGetErrors() {
            require_once ('Fixture/SimpleFormFixture.php');

            $SimpleFormFixture = new Fixture\SimpleFormFixture();
            $this->assertEquals(0, $SimpleFormFixture->getErrors());
            $SimpleFormFixture->setErrors();
            $this->assertEquals(1, $SimpleFormFixture->getErrors());
        }

        /**
         * Test if validation errors are recognized if they occured.
         * @covers Brickoo\Http\Form\SimpleForm::hasErrors
         */
        public function testHasErrors() {
            require_once ('Fixture/SimpleFormFixture.php');

            $SimpleFormFixture = new Fixture\SimpleFormFixture();
            $this->assertFalse($SimpleFormFixture->hasErrors());
            $SimpleFormFixture->setErrors();
            $this->assertTrue($SimpleFormFixture->hasErrors());
        }

        /**
         * Test if the success validation of the elements does return boolean true.
         * @covers Brickoo\Http\Form\SimpleForm::isValid
         */
        public function testIsValidWithSuccessValidation() {
            $parameters = array('some' => 'parameter');

            $Element = $this->getMock(
                'Brickoo\Http\Form\Element\Generic',
                array('isValid', 'getName'),
                array('name')
            );
            $Element->expects($this->once())
                    ->method('isValid')
                    ->with($this->equalTo($parameters))
                    ->will($this->returnValue(true));
            $Element->expects($this->once())
                    ->method('getName')
                    ->will($this->returnValue('name'));

            $this->SimpleForm->addElement($Element);
            $this->assertTrue($this->SimpleForm->isValid($parameters));
        }

        /**
         * Test if the failed validation of the elements does return boolean false
         * and the 2nd call does not execute the elements validation again.
         * @covers Brickoo\Http\Form\SimpleForm::isValid
         */
        public function testIsValidWithFailedValidation() {
            $parameters = array('some' => 'parameter');

            $Element = $this->getMock(
                'Brickoo\Http\Form\Element\Generic',
                array('isValid', 'getName'),
                array('name')
            );
            $Element->expects($this->once())
                    ->method('isValid')
                    ->with($this->equalTo($parameters))
                    ->will($this->returnValue(false));
            $Element->expects($this->any())
                    ->method('getName')
                    ->will($this->returnValue('name'));

            $this->SimpleForm->addElement($Element);
            $this->assertFalse($this->SimpleForm->isValid($parameters));
            $this->assertFalse($this->SimpleForm->isValid($parameters));
        }

        /**
         * Test if the SimpleForm has no errors an empty array is returned.
         * @covers Brickoo\Http\Form\SimpleForm::getElementsErrorMessages
         */
        public function testGetElementsErrorMessagesWithoutErrors() {
            $this->assertEquals(array(), $this->SimpleForm->getElementsErrorMessages());
        }

        /**
         * Test if the error message of elements can be retrieved.
         * @covers Brickoo\Http\Form\SimpleForm::getElementsErrorMessages
         */
        public function testGetElementsErrorMessagesWithErrors() {
            require_once ('Fixture/SimpleFormFixture.php');

            $errorMessages = array('The name is not valid.');
            $expectedMessages = array('name' => $errorMessages);

            $Element = $this->getMock(
                'Brickoo\Http\Form\Element\Generic',
                array('hasErrorMessages', 'getName', 'getErrorMessages'),
                array('name')
            );
            $Element->expects($this->once())
                    ->method('hasErrorMessages')
                    ->will($this->returnValue(true));
            $Element->expects($this->any())
                    ->method('getName')
                    ->will($this->returnValue('name'));
            $Element->expects($this->once())
                    ->method('getErrorMessages')
                    ->will($this->returnValue($errorMessages));

            $SimpleFormFixture = new Fixture\SimpleFormFixture();
            $SimpleFormFixture->addElement($Element);
            $SimpleFormFixture->setErrors();
            $this->assertEquals($expectedMessages, $SimpleFormFixture->getElementsErrorMessages());
        }

        /**
         * Test if the elements values can be retrieved.
         * @covers Brickoo\Http\Form\SimpleForm::getElementsValues
         */
        public function testGetElementsValues() {
            $expected = array('name' => 'BrickOO');

            $Element = $this->getMock(
                'Brickoo\Http\Form\Element\Generic',
                array('hasValue', 'getName', 'getValue'),
                array('name')
            );
            $Element->expects($this->once())
                    ->method('hasValue')
                    ->will($this->returnValue(true));
            $Element->expects($this->any())
                    ->method('getName')
                    ->will($this->returnValue('name'));
            $Element->expects($this->any())
                    ->method('getValue')
                    ->will($this->returnValue('BrickOO'));

            $this->SimpleForm->addElement($Element);
            $this->assertEquals($expected, $this->SimpleForm->getElementsValues());
        }

    }