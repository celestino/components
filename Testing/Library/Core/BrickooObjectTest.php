<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    use Brickoo\Library\Core\BrickooObject;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test case for the BrickooObject class.
     * @see Brickoo\Library\Core\BrickooObject
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */

    class BrickooObjectTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the BrickooObject.
         * @var object
         */
        protected $BrickooObject;

        /**
         * Set up the BrickooObject object used.
         */
        public function setUp()
        {
            $this->BrickooObject = new BrickooObject();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Core\BrickooObject::__construct
         * @covers Brickoo\Library\Core\Interfaces\BrickooSubstitutor
         */
        public function testBrickooObjectConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Core\BrickooObject',
                $this->BrickooObject
            );

            $BrickooMock = $this->getMock('\Brickoo\Library\Core\Interfaces\BrickooSubstitutor');
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Core\Interfaces\BrickooSubstitutor',
                $BrickooMock
            );

            $this->assertInstanceOf
            (
                '\Brickoo\Library\Core\BrickooObject',
                ($ownBrickoo =  new BrickooObject($BrickooMock))
            );
        }

        /**
         * Test if the static Brickoo object is used.
         * @covers Brickoo\Library\Core\BrickooObject::getRegistryEntry
         * @covers Brickoo\Library\Core\BrickooObject::addRegistryEntry
         */
        public function testGetRegistryEntry()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\Storage\Registry',
                $this->BrickooObject->addRegistryEntry('id', '007')
            );
            $this->assertEquals('007', $this->BrickooObject->getRegistryEntry('id'));
        }

        /**
         * Test if an not valid argument type throw an Exception.
         * @covers Brickoo\Library\Core\BrickooObject::getRegistryEntry
         * @expectedException InvalidArgumentException
         */
        public function testGetRegistryEntryArgumetException()
        {
            $this->BrickooObject->getRegistryEntry(array('wrongType'));
        }

        /**
         * Test if an not valid argument type throw an Exception.
         * @covers Brickoo\Library\Core\BrickooObject::addRegistryEntry
         * @expectedException InvalidArgumentException
         */
        public function testAddRegistryEntryArgumetException()
        {
            $this->BrickooObject->addRegistryEntry(array('wrongType'), 'value');
        }

        /**
         * Test if the replaced Brickoo object is used.
         * @covers Brickoo\Library\Core\BrickooObject::getRegistryEntry
         * @covers Brickoo\Library\Core\BrickooObject::addRegistryEntry
         */
        public function testGetRegistryEntryReplacement()
        {
            $BrickooStub = $this->getMock
            (
                '\Brickoo\Library\Core\Interfaces\BrickooSubstitutor',
                array('getRegistryEntry', 'addRegistryEntry')
            );
            $BrickooStub->expects($this->once())
                        ->method('getRegistryEntry')
                        ->will($this->returnValue('007'));
            $BrickooStub->expects($this->once())
                        ->method('addRegistryEntry')
                        ->will($this->returnValue(true));

            $BrickooObject = new BrickooObject($BrickooStub);
            $this->assertTrue($BrickooObject->addRegistryEntry('id', '007'));
            $this->assertEquals('007', $BrickooObject->getRegistryEntry('id'));
        }

    }

?>