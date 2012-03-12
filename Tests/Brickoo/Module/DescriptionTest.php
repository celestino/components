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

    class DescriptionTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var Description
         */
        protected $Description;

        /**
         * Sets up the Description instance used for hte tests.
         * @return void
         */
        protected function setUp()
        {
            $this->Description = new Description();
        }

        /**
         * Test if the Description class implements the DescriptionInterface.
         * Test if the status are available in the class property.
         * @covers Brickoo\Module\description::__construct
         */
        public function testConstructor()
        {
            $this->assertInstanceOf('Brickoo\Module\Interfaces\DescriptionInterface', $this->Description);
            $this->assertAttributeInternalType('array', 'availableStatus', $this->Description);
        }

        /**
         * Test if te modle name can be set and retrieved.
         * @covers Brickoo\Module\Description::getName
         * @covers Brickoo\Module\Description::setName
         */
        public function testGetSetName()
        {
            $this->assertSame($this->Description, $this->Description->setName('phpunit.test'));
            $this->assertAttributeEquals('phpunit.test', 'name', $this->Description);
            $this->assertEquals('phpunit.test', $this->Description->getName());
        }

        /**
         * Test if trying to retrieve the unset module name throws an exception.
         * @covers Brickoo\Module\Description::getName
         * @expectedException UnexpectedValueException
         */
        public function testGetNameValueExcpetion()
        {
            $this->Description->getName();
        }

        /**
         * Test if the vendor can be set and retrieved.
         * @covers Brickoo\Module\Description::getVendor
         * @covers Brickoo\Module\Description::setVendor
         */
        public function testGetSetVendor()
        {
            $this->assertSame($this->Description, $this->Description->setVendor('brickoo'));
            $this->assertAttributeEquals('brickoo', 'vendor', $this->Description);
            $this->assertEquals('brickoo', $this->Description->getVendor());
        }

        /**
         * Test if trying to retrieve a not available vendor throws an exception.
         * @covers Brickoo\Module\Description::getVendor
         * @expectedException UnexpectedValueException
         */
        public function testGetVendorValueException()
        {
            $this->Description->getVendor();
        }

        /**
         * Test if the website can be set and retrieved.
         * @covers Brickoo\Module\Description::getWebsite
         * @covers Brickoo\Module\Description::setWebsite
         */
        public function testGetSetWebsite()
        {
            $this->assertSame($this->Description, $this->Description->setWebsite('http://brickoo.home'));
            $this->assertAttributeEquals('http://brickoo.home', 'website', $this->Description);
            $this->assertEquals('http://brickoo.home', $this->Description->getWebsite());
        }

        /**
         * Test if trying to retrieve a not available website throws an exception.
         * @covers Brickoo\Module\Description::GetWebsite
         * @expectedException UnexpectedValueException
         */
        public function testGetWebsiteValueException()
        {
            $this->Description->getWebsite();
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Module\Description::setWebsite
         * @expectedException InvalidArgumentException
         */
        public function testSetWebsiteArgumentException()
        {
            $this->Description->setWebsite('fail');
        }

        /**
         * Test if the contact can be set and retrieved.
         * @covers Brickoo\Module\Description::getContact
         * @covers Brickoo\Module\Description::setContact
         */
        public function testGetSetContact()
        {
            $this->assertSame($this->Description, $this->Description->setContact('contact@brickoo'));
            $this->assertAttributeEquals('contact@brickoo', 'contact', $this->Description);
            $this->assertEquals('contact@brickoo', $this->Description->getContact());
        }

        /**
         * Test if trying to retrieve a not available contact throws an exception.
         * @covers Brickoo\Module\Description::getContact
         * @expectedException UnexpectedValueException
         */
        public function testGetContactValueException()
        {
            $this->Description->getContact();
        }

        /**
         * Test if the status can be set and retrieved.
         * @covers Brickoo\Module\Description::getStatus
         * @covers Brickoo\Module\Description::setStatus
         */
        public function testGetSetStatus()
        {
            $this->assertSame($this->Description, $this->Description->setStatus('stable'));
            $this->assertAttributeEquals('stable', 'status', $this->Description);
            $this->assertEquals('stable', $this->Description->getStatus());
        }

        /**
         * Test if trying to use a wrong argument throws an exception.
         * @covers Brickoo\Module\Description::setStatus
         * @expectedException InvalidArgumentException
         */
        public function testSetStatusArgumentException()
        {
            $this->Description->setStatus('undefined');
        }

        /**
         * Test if the version can be set and retrieved.
         * @covers Brickoo\Module\Description::getVersion
         * @covers Brickoo\Module\Description::setVersion
         */
        public function testGetSetVersion()
        {
            $this->assertSame($this->Description, $this->Description->setVersion('v3.0'));
            $this->assertAttributeEquals('v3.0', 'version', $this->Description);
            $this->assertEquals('v3.0', $this->Description->getVersion());
        }

        /**
         * Test if the description can be set and retrieved.
         * @covers Brickoo\Module\Description::getDescription
         * @covers Brickoo\Module\Description::setDescription
         */
        public function testGetSetDescription()
        {
            $this->assertSame($this->Description, $this->Description->setDescription('some description'));
            $this->assertAttributeEquals('some description', 'description', $this->Description);
            $this->assertEquals('some description', $this->Description->getDescription());
        }

        /**
         * Test if the description can be returned as string.
         * @covers Brickoo\Module\description::toString
         * @covers Brickoo\Module\description::__toString
         */
        public function testDescriptionToString()
        {
            $expected = '';
            $expected .= "Name: Brickoo Test Module\n";
            $expected .= "Vendor: Brickoo\n";
            $expected .= "Website: http://brickoo.test\n";
            $expected .= "Contact: contact@brickoo.test\n";
            $expected .= "Status: stable\n";
            $expected .= "Version: 3.0\n";
            $expected .= "Description: some description text";

            $this->Description->setName('Brickoo Test Module')
                              ->setVendor('Brickoo')
                              ->setWebsite('http://brickoo.test')
                              ->setContact('contact@brickoo.test')
                              ->setStatus('stable')
                              ->setVersion('3.0')
                              ->setDescription('some description text');

            $this->assertEquals($expected, $this->Description->toString());
            $this->assertEquals($expected, (string)$this->Description);
        }

    }