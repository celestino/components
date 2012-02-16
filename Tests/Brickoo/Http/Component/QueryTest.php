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

    use Brickoo\Library\Http\Component\Query;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * UrlTest
     *
     * Test suite for the Url class.
     * @see Brickoo\Library\Component\Url
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class QueryTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Query class.
         * @var \Brickoo\Library\Http\Component\Query
         */
        protected $Query;

        /**
         * Sets up the Query instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->Query = new Query();
        }

        /**
         * Test if the query parameters can be imported from the globals $_GET.
         * @covers Brickoo\Library\Http\Component\Query::importFromGlobals
         */
        public function testImportFromGlobals()
        {
            $_GET['some'] = 'value';
            $this->assertSame($this->Query, $this->Query->importFromGlobals());
            $this->assertAttributeEquals(array('some' => 'value'), 'container', $this->Query);
            unset($_GET['some']);
        }

        /**
         * Test if the query parameters can be imported from string.
         * @covers Brickoo\Library\Http\Component\Query::importFromString
         */
        public function testImportFromString()
        {
            $expectedParameters = array
            (
                'param1' => 'value1',
                'param2' => 'value2',
                'param3' => 'value3'
            );
            $query = '?param1=value1&param2=value2&param3=value3';

            $this->assertEquals($this->Query, $this->Query->importFromString($query));
            $this->assertAttributeEquals($expectedParameters, 'container', $this->Query);
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Component\Query::importFromString
         * @expectedException InvalidArgumentException
         */
        public function testImportFromStringArgumentException()
        {
            $this->Query->importFromString(array('wrongType'));
        }

        /**
         * Test if the quer paramters can be converted to string.
         * @covers Brickoo\Library\Http\Component\Query::toString
         * @covers Brickoo\Library\Http\Component\Query::__toString
         */
        public function testToString()
        {
            $expectedQuery = 'param1=value1&param2=value2&param3=value3';

            $queryParameters = array
            (
                'param1' => 'value1',
                'param2' => 'value2',
                'param3' => 'value3'
            );
            $this->Query->merge($queryParameters);
            $this->assertEquals($expectedQuery, $this->Query->toString());
            $this->assertEquals($expectedQuery, (string)$this->Query);
        }

    }