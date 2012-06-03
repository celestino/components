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

    namespace Tests\Brickoo\Http\Form\Converter;

    use Brickoo\Http\Form\Converter\ToArray;

    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the ToArray class.
     * @see Brickoo\Http\Form\Converter\ToArray
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ToArrayTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Convert\ToArray class.
         * @var \Brickoo\Http\Form\Converter\ToArray
         */
        protected $Converter;

        /**
         * Sets up the Converter\ToArray dependency used in the test cases.
         * @return void
         */
        protected function setUp() {
            $this->Converter = new ToArray();
        }

        /**
         * Test if the form container contains a simplified form represantation.
         * @covers Brickoo\Http\Form\Converter\ToArray::getFormContainer
         */
        public function testGetFormContainer()
        {
            $Form = $this->getMock('Brickoo\Http\Form\Interfaces\Form');
            $Form->expects($this->once())
                 ->method('getErrors')
                 ->will($this->returnValue(5));

            $expected = array (
                ToArray::FORM_CONTAINER_ERRORS => 5,
                ToArray::FORM_CONTAINER_ELEMENTS => array()
            );

            $this->assertEquals($expected, $this->Converter->getFormContainer($Form));
        }

        /**
         * Test if the elements container contnains a simplified element representation.
         * @covers Brickoo\Http\Form\Converter\ToArray::getElementContainer
         */
        public function testGetElementContainer()
        {
            $Element = $this->getMock('Brickoo\Http\Form\Element\Interfaces\Element');
            $Element->expects($this->once())
                    ->method('isRequired')
                    ->will($this->returnValue(true));
            $Element->expects($this->once())
                    ->method('getName')
                    ->will($this->returnValue('test'));
            $Element->expects($this->once())
                    ->method('getValue')
                    ->will($this->returnValue('BrickOO'));
            $Element->expects($this->once())
                    ->method('getErrorMessages')
                    ->will($this->returnValue(array('The name `Brickoo` is not expected.')));

            $expected = array(
                ToArray::ELEMENT_CONTAINER_REQUIRED => true,
                ToArray::ELEMENT_CONTAINER_NAME => 'test',
                ToArray::ELEMENT_CONTAINER_VALUE => 'BrickOO',
                ToArray::ELEMENT_CONTAINER_ERROR_MESSAGES => array('The name `Brickoo` is not expected.')
            );

            $this->assertEquals($expected, $this->Converter->getElementContainer($Element));
        }

        /**
         * Test if the form can be converted to an array representation.
         * @covers Brickoo\Http\Form\Converter\ToArray::convert
         * @depends testGetFormContainer
         */
        public function testConvert()
        {
            $Element = $this->getMock('Brickoo\Http\Form\Element\Interfaces\Element');
            $Element->expects($this->once())
                    ->method('isRequired')
                    ->will($this->returnValue(true));
            $Element->expects($this->once())
                    ->method('getName')
                    ->will($this->returnValue('test'));
            $Element->expects($this->once())
                    ->method('getValue')
                    ->will($this->returnValue('BrickOO'));
            $Element->expects($this->once())
                    ->method('getErrorMessages')
                    ->will($this->returnValue(array('The name `Brickoo` is not expected.')));

            $Form = $this->getMock('Brickoo\Http\Form\Interfaces\Form');
            $Form->expects($this->once())
                 ->method('getErrors')
                 ->will($this->returnValue(1));
            $Form->expects($this->once())
                 ->method('hasElements')
                 ->will($this->returnValue(true));
            $Form->expects($this->once())
                 ->method('getElements')
                 ->will($this->returnValue(array($Element)));

            $expected = array(
                ToArray::FORM_CONTAINER_ERRORS => 1,
                ToArray::FORM_CONTAINER_ELEMENTS => array(
                    array(
                        ToArray::ELEMENT_CONTAINER_REQUIRED => true,
                        ToArray::ELEMENT_CONTAINER_NAME => 'test',
                        ToArray::ELEMENT_CONTAINER_VALUE => 'BrickOO',
                        ToArray::ELEMENT_CONTAINER_ERROR_MESSAGES => array('The name `Brickoo` is not expected.')
                    )
                )
            );

            $this->assertEquals($expected, $this->Converter->convert($Form));

        }

    }