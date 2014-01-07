<?php

    /*
     * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Routing\Route;

    use Brickoo\Routing\Route\Route;

    /**
     * RouteTest
     *
     * Test suite for the Route class.
     * @see Brickoo\Routing\Route\Route
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouteTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Route\Route::__construct
         */
        public function testConstructor() {
            $routeName = 'test.route';
            $routePath = '/path/{page}/{pageNumber}';
            $routeController = 'SomeController';
            $routeAction = 'someAction';
            $routeRules = array('page' => '[a-z]');
            $routeDefaultValues = array('pageNumber' => 1);

            $Route = new Route(
                $routeName, $routePath, $routeController, $routeAction,
                $routeRules, $routeDefaultValues
            );
            $this->assertInstanceof('Brickoo\Routing\Route\Interfaces\Route', $Route);
            $this->assertAttributeEquals($routeName, 'name', $Route);
            $this->assertAttributeEquals($routePath, 'path', $Route);
            $this->assertAttributeEquals($routeController, 'controller', $Route);
            $this->assertAttributeEquals($routeAction, 'action', $Route);
            $this->assertAttributeEquals($routeRules, 'rules', $Route);
            $this->assertAttributeEquals($routeDefaultValues, 'defaultValues', $Route);
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getName
         */
        public function testGeName() {
            $Route = $this->getRouteFixture();
            $this->assertEquals('test.route',$Route->getName());
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getPath
         */
        public function testGetPath() {
            $Route = $this->getRouteFixture();
            $this->assertEquals('/article/{name}/{page}', $Route->getPath());
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getController
         */
        public function testGetController() {
            $Route = $this->getRouteFixture();
            $this->assertEquals('NewspaperController', $Route->getController());
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getAction
         */
        public function testGetAction() {
            $Route = $this->getRouteFixture();
            $this->assertEquals('getArticle', $Route->getAction());
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getRules
         */
        public function testGetRules() {
            $expectedRules = array(
                'name' => '[\w\-]+',
                'page' => '[0-9]+'
            );
            $Route = $this->getRouteFixture();
            $this->assertEquals($expectedRules, $Route->getRules());
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getRule
         */
        public function testGetRule() {
            $Route = $this->getRouteFixture();
            $this->assertEquals('[\w\-]+', $Route->getRule('name'));
            $this->assertEquals('[0-9]+', $Route->getRule('page'));
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getRule
         * @expectedException InvalidArgumentException
         */
        public function testGetRuleThrowsArgumentException() {
            $Route = $this->getRouteFixture();
            $Route->getRule(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getRule
         * @expectedException UnexpectedValueException
         */
        public function testGetRuleThrowsUnexpectedException() {
            $Route = $this->getRouteFixture();
            $Route->getRule('rule.does.not.exist');
        }

        /**
         * @covers Brickoo\Routing\Route\Route::hasRules
         */
        public function testHasRules() {
            $Route = $this->getRouteFixture();
            $this->assertTrue($Route->hasRules());
        }

        /**
         * @covers Brickoo\Routing\Route\Route::hasRule
         */
        public function testHasRule() {
            $Route = $this->getRouteFixture();
            $this->assertFalse($Route->hasRule('rule.does.not.exist'));
            $this->assertTrue($Route->hasRule('name'));
            $this->assertTrue($Route->hasRule('page'));
        }

        /**
         * @covers Brickoo\Routing\Route\Route::hasRule
         * @expectedException InvalidArgumentException
         */
        public function testHasRuleThrowsArgumentException() {
            $Route = $this->getRouteFixture();
            $Route->hasRule(array('wrongType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getDefaultValues
         */
        public function testGetDefaultValues() {
            $expectedResult = array('page' => 1);
            $Route = $this->getRouteFixture();
            $this->assertEquals($expectedResult, $Route->getDefaultValues());
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getDefaultValue
         */
        public function testGetDefaultValue() {
            $Route = $this->getRouteFixture();
            $this->assertEquals(1, $Route->getDefaultValue('page'));
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getDefaultValue
         * @expectedException InvalidArgumentException
         */
        public function testGetDefaultValueThrowsArgumentExpception() {
            $Route = $this->getRouteFixture();
            $Route->getDefaultValue(array('wringType'));
        }

        /**
         * @covers Brickoo\Routing\Route\Route::getDefaultValue
         * @expectedException UnexpectedValueException
         */
        public function testGetDefaultvalueThrowsUnexpectedValueException() {
            $Route = $this->getRouteFixture();
            $Route->getDefaultValue('rule.does.not.exist');
        }

        /**
         * @covers Brickoo\Routing\Route\Route::hasDefaultValue
         */
        public function testHasDefaultValue() {
            $Route = $this->getRouteFixture();
            $this->assertFalse($Route->hasDefaultValue('rule.does.not.exist'));
            $this->assertTrue($Route->hasDefaultValue('page'));
        }

        /**
         * @covers Brickoo\Routing\Route\Route::hasDefaultValue
         * @expectedException InvalidArgumentException
         */
        public function testHasDefaultValueThrowsInvalidArgumentExcpetion() {
            $Route = $this->getRouteFixture();
            $Route->hasDefaultValue(array('wrongType'));
        }

        /**
         * Returns a route fixture for the test cases.
         * @return \Brickoo\Routing\Route\Route
         */
        private function getRouteFixture() {
            return new Route(
                'test.route',
                 '/article/{name}/{page}',
                 'NewspaperController',
                 'getArticle',
                 array(
                     'name' => '[\w\-]+',
                     'page' => '[0-9]+'
                 ),
                 array('page' => 1)
            );
        }

    }