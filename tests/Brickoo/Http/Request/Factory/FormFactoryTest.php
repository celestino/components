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

    namespace Tests\Brickoo\Http\Request\Factory;

    use Brickoo\Http\Request\Factory\FormFactory;

    /**
     * FormFactoryTest
     *
     * Test suite for the FormFactory class.
     * @see Brickoo\Http\Request\Factory\FormFactory
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FormFactoryTest extends \PHPUnit_Framework_TestCase {

        /** @var array */
        private $backupPostVars;

        /**
         * {@inheritDoc}
         * Backups the global post values.
         * PHPUnit provides this by annotation (@backupGlobals),
         * to do not depend on PHPUnit implementation this is done manually.
         * @see PHPUnit_Framework_TestCase::setUp()
         * @return void
         */
        public function setUp() {
            $this->backupPostVars = $_POST;
        }

        /**
         * {@inheritDoc}
         * Restores the global post values.
         * @see PHPUnit_Framework_TestCase::tearDown()
         * @return void
         */
        public function tearDown() {
            $_POST = $this->backupPostVars;
        }

        /**
         * @covers Brickoo\Http\Request\Factory\FormFactory::Create
         */
        public function testCreateFromGlobals() {
            $_POST = array ("test" => "passed");

            $Form = FormFactory::Create();
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Form', $Form);
            $this->assertEquals($_POST, $Form->toArray());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\FormFactory::CreateFromString
         */
        public function testImportFromString() {
            $expectedParameters = array(
                "param1" => "value 1",
                "param2" => "value 2",
                "param3" => "value3"
            );
            $Form = "param1=value%201&param2=value+2&param3=value3";

            $Form = FormFactory::CreateFromString($Form);
            $this->assertInstanceOf('Brickoo\Http\Request\Interfaces\Form', $Form);
            $this->assertEquals($expectedParameters, $Form->toArray());
        }

        /**
         * @covers Brickoo\Http\Request\Factory\FormFactory::CreateFromString
         * @expectedException InvalidArgumentException
         */
        public function testCreateFromStringThrowsInvalidArgumentException() {
            FormFactory::CreateFromString(array("wrongType"));
        }

    }