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

    use Brickoo\Module\Description;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * DescriptionTest
     *
     * Test suite for the Description class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class DescriptionTest extends \PHPUnit_Framework_TestCase {

       /**
         * Test if the Description class implements the Description interface.
         * Test if the collection property is an instance of the \Brickoo\Memory\Container
         * @covers Brickoo\Module\description::__construct
         */
        public function testConstructor() {
            $Description = new Description();
            $this->assertInstanceOf('Brickoo\Module\Interfaces\Description', $Description);
            $this->assertAttributeInstanceOf('Brickoo\Memory\Container', 'InformationCollection', $Description);
        }

        /**
         * Test if an Information element canbe added to the collection.
         * @covers Brickoo\Module\Description::add
         */
        public function testAdd() {
            $Information = $this->getMock(
                'Brickoo\Module\Component\GenericInformation', array('getName'), array('name', null)
            );
            $Information->expects($this->any())
                        ->method('getName')
                        ->will($this->returnValue('name'));

            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('has')
                      ->with('name')
                      ->will($this->returnValue(false));
            $Container->expects($this->once())
                      ->method('set')
                      ->with('name', $Information);

            $Description = new Description($Container);
            $this->assertEquals($Description, $Description->add($Information));
        }

        /**
         * Test if trying to overwritte an informationw ith the same key throws an exception.
         * @covers Brickoo\Module\Description::add
         * @covers \Brickoo\Core\Exceptions\ValueOverwriteException
         * @expectedException \Brickoo\Core\Exceptions\ValueOverwriteException
         */
        public function testAddOverwritteException() {
            $Information = $this->getMock(
                'Brickoo\Module\Component\GenericInformation', array('getName'), array('name', null)
            );
            $Information->expects($this->any())
                        ->method('getName')
                        ->will($this->returnValue('name'));

            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('has')
                      ->with('name')
                      ->will($this->returnValue(true));

            $Description = new Description($Container);
            $this->assertEquals($Description, $Description->add($Information));
        }

        /**
         * Test if an information name can be checked as available through the collection container.
         * @covers Brickoo\Module\Description::has
         */
        public function testHas() {
            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('has')
                      ->with('name')
                      ->will($this->returnValue(true));

            $Description = new Description($Container);
            $this->assertTrue($Description->has('name'));
        }

        /**
         * Test if trying to use a wrong argument an exception is throwed.
         * @covers Brickoo\Module\Description::has
         * @expectedException \InvalidArgumentException
         */
        public function testHasArgumentException() {
            $Description = new Description();
            $Description->has(array('wrongType'));
        }

        /**
         * Test if an information value could be retrieved.
         * @covers Brickoo\Module\Description::get
         */
        public function testGet() {
            $moduleName = 'name';

            $Information = $this->getMock(
                'Brickoo\Module\Component\GenericInformation', array('get'), array($moduleName, 'testValue')
            );
            $Information->expects($this->any())
                        ->method('get')
                        ->will($this->returnValue('testValue'));

            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('get')
                      ->with($moduleName)
                      ->will($this->returnValue($Information));

            $Description = new Description($Container);
            $this->assertEquals('testValue', $Description->get($moduleName));
        }

        /**
         * Test if trying to retrieve an information which does not exist throws an exception.
         * @covers Brickoo\Module\Description::get
         * @expectedException \UnexpectedValueException
         */
        public function testGetValueException() {
            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('get')
                      ->with('name')
                      ->will($this->returnValue(false));

            $Description = new Description($Container);
            $this->assertEquals('testValue', $Description->get('name'));
        }

        /**
         * Test if trying to use a not valid argument throws an exception.
         * @covers Brickoo\Module\Description::get
         * @expectedException \InvalidArgumentException
         */
        public function testGetInvalidArgumentException() {
            $Description = new Description();
            $this->assertEquals('testValue', $Description->get(array('wrongType')));
        }

        /**
         * Test if the complete information collected could be retrieved.
         * @covers Brickoo\Module\Description::getAll
         */
        public function testGetAll() {
            $informationCollected = array('information objects');

            $Container = $this->getMock('Brickoo\Memory\Container');
            $Container->expects($this->once())
                      ->method('toArray')
                      ->will($this->returnValue($informationCollected));

            $Description = new Description($Container);
            $this->assertEquals($informationCollected, $Description->getAll());
        }

        /**
         * Test if the description can be returned as string.
         * @covers Brickoo\Module\description::toString
         * @covers Brickoo\Module\description::__toString
         */
        public function testDescriptionToString() {
            $expected = "name : Test Module\r\n";
            $expected .= "vendor : BrickOO\r\n";

            $InformationName = $this->getMock(
                'Brickoo\Module\Component\GenericInformation', array('getName', 'toString'), array('name', 'Test Module')
            );
            $InformationName->expects($this->any())
                            ->method('getName')
                            ->will($this->returnValue('name'));
            $InformationName->expects($this->any())
                            ->method('toString')
                            ->will($this->returnValue('Test Module'));

            $InformationVendor = $this->getMock(
                'Brickoo\Module\Component\GenericInformation', array('getName', 'toString'), array('vendor', 'BrickOO')
            );
            $InformationVendor->expects($this->any())
                              ->method('getName')
                              ->will($this->returnValue('vendor'));
            $InformationVendor->expects($this->any())
                              ->method('toString')
                              ->will($this->returnValue('BrickOO'));

            $Description = new Description();
            $Description->add($InformationName)->add($InformationVendor);

            $this->assertEquals($expected, $Description->toString());
            $this->assertEquals($expected, (string)$Description);
        }

    }