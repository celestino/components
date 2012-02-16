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

    use Brickoo\Library\Config\ConfigurationNamespace;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * ConfigurationNamespaceTest
     *
     * Test suite for the ConfigurationNamespace class.
     * @see Brickoo\Library\Config\ConfigurationNamespace
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ConfigurationNamespaceTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Test if the ConfigurationNamespace implements the Config\Interfaces\ConfiguratioNamespaceInterface.
         * Test if the namespace class property is set.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::__construct
         * @covers Brickoo\Library\Config\ConfigurationNamespace::GetReservedNamespaces
         * @covers Brickoo\Library\Config\ConfigurationNamespace::AddReservedNamespace
         */
        public function testConstructor()
        {
            $ConfigFixture = new ConfigFixture('brickoo');
            $this->assertInstanceOf
            (
                'Brickoo\Library\Config\Interfaces\ConfigurationNamespaceInterface',
                $ConfigFixture
            );
            $this->assertAttributeEquals('brickoo', 'namespace', $ConfigFixture);
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @expectedException InvalidArgumentException
         */
        public function testConstructArgumentException()
        {
            $Fail = new ConfigFixture(array('wrongType'));
        }

        /**
         * Test if trying to reserve a already reserved namespace throws an exception.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::__construct
         * @covers Brickoo\Library\Config\Exceptions\NamespaceReservedException::__construct
         * @expectedException Brickoo\Library\Config\Exceptions\NamespaceReservedException
         */
        public function testConstructNamespaceException()
        {
            $ConfigFixture_A = new ConfigFixture('brickoo');
            $ConfigFixture_B = new ConfigFixture('brickoo');
        }

        /**
         * Test if all reserved namspaces can be retrieved.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::GetReservedNamespaces
         */
        public function testGetReservedNamespaces()
        {
            $ConfigFixture_A = new ConfigFixture('brickoo');
            $ConfigFixture_B = new ConfigFixture('modules');
            $this->assertEquals(array('brickoo', 'modules'), ConfigFixture::GetReservedNamespaces());
        }

        /**
         * Test if the a reserved namespace is recoginized.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::IsNamespaceReserved
         */
        public function testIsNamespaceReserved()
        {
            $ConfixFixture = new ConfigFixture('brickoo');
            $this->assertTrue(ConfigFixture::IsNamespaceReserved('brickoo'));
            $this->assertFalse(ConfigFixture::IsNamespaceReserved('modules'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @expectedException InvalidArgumentException
         */
        public function testIsNamespaceReservedArgumentException()
        {
            ConfigFixture::IsNamespaceReserved(array('wrongType'));
        }

        /**
         * Test if the object namespace can be retrieved.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::getNamespace
         */
        public function testGetNamespace()
        {
            $ConfigFixture = new ConfigFixture('brickoo');
            $this->assertEquals('brickoo', $ConfigFixture->getNamespace());
        }

        /**
         * Test if a configuration can be stored and the ConfigurationNamespace reference is returned.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::setConfiguration
         */
        public function testSetConfiguration()
        {
            $ConfigFixture = new ConfigFixture('brickoo');
            $this->assertSame($ConfigFixture, $ConfigFixture->setConfiguration('name', 'new name'));
            $this->assertAttributeEquals(array('name' => 'new name'), 'configuration', $ConfigFixture);
        }

        /**
         * Test if trying to overwrite a configuration throws an exception
         * @covers Brickoo\Library\Config\ConfigurationNamespace::setConfiguration
         * @covers Brickoo\Library\Core\Exceptions\ValueOverwriteException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\ValueOverwriteException
         */
        public function testSetConfigurationOverwriteException()
        {
            $ConfigFixture = new ConfigFixture('brickoo');
            $ConfigFixture->setConfiguration('name', 'new name');
            $ConfigFixture->setConfiguration('name', 'other name');
        }

        /**
         * Test if a configuration identifier is recognized as available.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::hasConfiguration
         */
        public function testHasConfiguration()
        {
            $ConfigFixture = new ConfigFixture('brickoo');
            $this->assertSame($ConfigFixture, $ConfigFixture->setConfiguration('name', 'new name'));
            $this->assertAttributeEquals(array('name' => 'new name'), 'configuration', $ConfigFixture);

            $this->assertTrue($ConfigFixture->hasConfiguration('name'));
            $this->assertFalse($ConfigFixture->hasConfiguration('false'));
        }

        /**
         * Test if the configuration can be retrieved.
         * @covers Brickoo\Library\Config\ConfigurationNamespace::getConfiguration
         */
        public function testGetConfiguration()
        {
            $ConfigFixture = new ConfigFixture('brickoo');
            $this->assertSame($ConfigFixture, $ConfigFixture->setConfiguration('name', 'new name'));
            $this->assertAttributeEquals(array('name' => 'new name'), 'configuration', $ConfigFixture);

            $this->assertEquals('new name', $ConfigFixture->getConfiguration('name'));
            $this->assertEquals('DEFAULT', $ConfigFixture->getConfiguration('default', 'DEFAULT'));
        }

    }

    /**
     * Fixture to resolve the problem with the static reserved namespaces holder.
     */
    class ConfigFixture extends ConfigurationNamespace
    {
        public function __destruct()
        {
            static::$ReservedNamespaces = array();
        }
    }