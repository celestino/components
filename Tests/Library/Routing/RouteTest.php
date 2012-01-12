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

    use Brickoo\Library\Routing\Route;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RouteTest
     *
     * Test suite for the Route class.
     * @see Brickoo\Library\Routing\Route
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouteTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Route class.
         * @var Route
         */
        protected $Route;

        /**
         * Sets the Route instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->Route = new Route();
        }

        /**
        * Test if the path can be set and the Route reference is returned.
        * @covers Brickoo\Library\Routing\Route::setPath
        */
        public function testSetPath()
        {
            $this->assertSame($this->Route, $this->Route->setPath('/path/to/resource'));
            $this->assertAttributeEquals('/path/to/resource', 'path', $this->Route);

            return $this->Route;
        }

        /**
         * Test if trying to set a wrong argument type throws an exception.
         * @covers Brickoo\Library\Routing\Route::setPath
         * @expectedException InvalidArgumentException
         */
        public function testSetPathArgumentException()
        {
            $this->Route->setPath(array('wrongType'));
        }

        /**
         * Test if the path property can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getPath
         * @depends testSetPath
         */
        public function testGetPath($Route)
        {
            $this->assertEquals('/path/to/resource', $Route->getPath());
        }

        /**
         * Test if trying to retrieve the empty path property throws an exception.
         * @covers Brickoo\Library\Routing\Route::getPath
         * @expectedException UnexpectedValueException
         */
        public function testGetPathValueException()
        {
            $this->Route->getPath();
        }

        /**
         * Test if the controller can be set and the Route reference is returned.
         * @covers Brickoo\Library\Routing\Route::setController
         */
        public function testSetController()
        {
            $this->assertSame($this->Route, $this->Route->setController('controller::method'));
            $this->assertAttributeEquals('controller::method', 'controller', $this->Route);

            return $this->Route;
        }

        /**
         * Test if trying to set a wrong argument type throws an exception.
         * @covers Brickoo\Library\Routing\Route::setController
         * @expectedException InvalidArgumentException
         */
        public function testSetControllerArgumentException()
        {
            $this->Route->setController('controller:method');
        }

        /**
         * Test if the controller property can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getController
         * @depends testSetController
         */
        public function testGetController($Route)
        {
            $this->assertEquals('controller::method', $Route->getController());
        }

        /**
         * Test if trying to retrieve the empty controller property throws an exception.
         * @covers Brickoo\Library\Routing\Route::getController
         * @expectedException UnexpectedValueException
         */
        public function testGetControllerValueException()
        {
            $this->Route->getController();
        }

        /**
         * Test if the method can be set and the Route reference is returned.
         * @covers Brickoo\Library\Routing\Route::setMethod
         */
        public function testSetMethod()
        {
            $this->assertSame($this->Route, $this->Route->setMethod('POST'));
            $this->assertAttributeEquals('POST', 'method', $this->Route);

            return $this->Route;
        }

        /**
         * Test if trying to set a wrong argument type throws an exception.
         * @covers Brickoo\Library\Routing\Route::setMethod
         * @expectedException InvalidArgumentException
         */
        public function testSetMethodArgumentException()
        {
            $this->Route->setMethod(array('wrongType'));
        }

        /**
         * Test if the method property can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getMethod
         * @depends testSetMethod
         */
        public function testGetMethod($Route)
        {
            $this->assertEquals('POST', $Route->getMethod());
        }

        /**
         * Test if the hostname can be set and the Route reference is returned.
         * @covers Brickoo\Library\Routing\Route::setHostname
         */
        public function testSetHostname()
        {
            $this->assertSame($this->Route, $this->Route->setHostname('domain.com'));
            $this->assertAttributeEquals('domain.com', 'hostname', $this->Route);

            return $this->Route;
        }

        /**
         * Test if trying to set a wrong argument type throws an exception.
         * @covers Brickoo\Library\Routing\Route::setHostname
         * @expectedException InvalidArgumentException
         */
        public function testSetHostnameArgumentException()
        {
            $this->Route->setHostname(array('wrongType'));
        }

        /**
         * Test if the hostname property can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getHostname
         * @depends testSetHostname
         */
        public function testGetHostname($Route)
        {
            $this->assertEquals('domain.com', $Route->getHostname());
        }

        /**
         * Test if the session configuration can be set and the Route reference is returned.
         * @covers Brickoo\Library\Routing\Route::setSessionConfiguration
         */
        public function testSetSessionConfiguration()
        {
            $config = array('name' =>  'sessionX');
            $this->assertSame($this->Route, $this->Route->setSessionConfiguration($config));
            $this->assertAttributeEquals($config, 'sessionConfiguration', $this->Route);

            return $this->Route;
        }

        /**
         * Test if the sessionConfiguration property can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getSessionConfiguration
         * @depends testSetSessionConfiguration
         */
        public function testGetSessionConfiguration($Route)
        {
            $this->assertEquals(array('name' => 'sessionX'), $Route->getSessionConfiguration());
        }

        /**
         * Test if trying to retrieve the empty method property throws an exception.
         * @covers Brickoo\Library\Routing\Route::getMethod
         * @expectedException UnexpectedValueException
         */
        public function testGetMethodValueException()
        {
            $this->Route->getMethod();
        }

        /**
         * Test if a default value can be added and the  Route reference ist returned.
         * @covers Brickoo\Library\Routing\Route::addDefaultValue
         */
        public function testAddDefaultValue()
        {
            $this->assertSame($this->Route, $this->Route->addDefaultValue('some', 'value'));
            $this->assertAttributeEquals(array('some' => 'value'), 'defaultValues', $this->Route);

            return $this->Route;
        }

        /**
         * Test if passign an wrong arguemtn type throws an exception.
         * @covers Brickoo\Library\Routing\Route::addDefaultValue
         * @expectedException InvalidArgumentException
         */
        public function testAddDefaultValueArgumentException()
        {
            $this->Route->addDefaultValue(array('wrongType'), 'someValue');
        }

        /**
         * Test if all default values can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getDefaultValues
         * @depends testAddDefaultValue
         */
        public function testGetDefaultValues($Route)
        {
            $this->assertEquals(array('some' => 'value'), $Route->getDefaultValues());
        }

        /**
         * Test if a default value can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getDefaultValue
         * @depends testAddDefaultValue
         */
        public function testGetDefaultValue($Route)
        {
            $this->assertEquals('value', $Route->getDefaultValue('some'));
        }

        /**
         * Test if trying passing a wrong arguemnt type throws an exception.
         * @covers Brickoo\Library\Routing\Route::getDefaultValue
         * @expectedException InvalidArgumentException
         */
        public function testGetDefaultValueArgumentException()
        {
            $this->Route->getDefaultValue(array('wrongType'));
        }

        /**
         * Test if trying to retrieve a not existing default valeu throws an exception.
         * @covers Brickoo\Library\Routing\Route::getDefaultValue
         * @expectedException UnexpectedValueException
         */
        public function testGetDefaultValueException()
        {
            $this->Route->getDefaultValue('someParameter');
        }

        /**
         * Test if a defaultvalue can be located and the check result si returned.
         * @covers Brickoo\Library\Routing\Route::hasDefaultValue
         * @depends testAddDefaultValue
         */
        public function testHasDefaultValue($Route)
        {
            $this->assertTrue($Route->hasDefaultValue('some'));
        }

        /**
         * Test if a  rule can be added and the Route reference is returned.
         * @covers Brickoo\Library\Routing\Route::addRule
         */
        public function testAddRule()
        {
            $this->assertSame($this->Route, $this->Route->addRule('name', '[a-z]+'));
            $this->assertAttributeEquals(array('name' => '[a-z]+'), 'rules', $this->Route);

            return $this->Route;
        }

        /**
         * Test if trying to add a wrong argument type as parameter throws an exception.
         * @covers Brickoo\Library\Routing\Route::addRule
         * @expectedException InvalidArgumentException
         */
        public function testAddRuleArgumentException()
        {
            $this->Route->addRule(array('wrongType'), 'regex');
        }

        /**
         * Test if all ruels can be retrieved.
         * @covers Brickoo\Library\Routing\Route::getRules
         * @depends testAddRule
         */
        public function testGetRules($Route)
        {
            $this->assertEquals(array('name' => '[a-z]+'), $Route->getRules());
        }

        /**
         * Test f a rule can be retrieved by its parameter.
         * @covers Brickoo\Library\Routing\Route::getRule
         * @depends testAddRule
         */
        public function testGetRule($Route)
        {
            $this->assertEquals('[a-z]+', $Route->getRule('name'));
        }

        /**
         * Test if a wrong argument type trwos an exception.
         * @covers Brickoo\Library\Routing\Route::getRule
         * @expectedException InvalidArgumentException
         */
        public function testGetRuleArgumentException()
        {
            $this->Route->getRule(array('wrongType'));
        }

        /**
         * Test if trying to retrieve a not available rule thrwos an exception.
         * @covers Brickoo\Library\Routing\Route::getRule
         * @expectedException UnexpectedValueException
         */
        public function testGetRuleValueException()
        {
            $this->Route->getRule('fail');
        }

        /**
         * Test if an assigned rule is recognized by its parameter.
         * @covers Brickoo\Library\Routing\Route::hasRule
         * @depends testAddRule
         */
        public function testHasRule($Route)
        {
            $this->assertTrue($Route->hasRule('name'));
        }

        /**
         * Test if the Route instance is implemnting the ROuteInterface.
         * @covers Brickoo\Library\ROuting\Route::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceof('Brickoo\Library\Routing\Interfaces\RouteInterface', $this->Route);
        }

        /**
         * Test if the amount of segments is counted implenting the Countable interface.
         * @covers Brickoo\Library\Routing\Route::count
         * @depends testSetPath
         */
        public function testCount($Route)
        {
            $this->assertEquals(3, count($Route));
        }

    }

?>
