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

    use Brickoo\Library\Memory\Registry;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RegistryTest
     *
     * Test suite for the Registry class.
     * @see Brickoo\Library\Memory\Registry
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RegistryTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Registry class.
         * @var object Registry
         */
        public $Registry;

        /**
         * Set up the Registry object used.
         * @return void
         */
        public function setUp()
        {
            $this->Registry = new Registry();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Memory\Registry::__construct
         */
        public function testRegistryConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Memory\Registry',
                ($Registry = new Registry())
            );
        }

        /**
         * Test if the identifier can be registered.
         * @covers Brickoo\Library\Memory\Registry::getAll
         */
        public function testGetRegistrations()
        {
            $this->assertInternalType('array', $this->Registry->getAll());
            $this->Registry->register('name', 'john');
            $this->assertArrayHasKey('name', $this->Registry->getAll());
        }

         /**
         * Test if the registrations can be added as an array.
         * @covers Brickoo\Library\Memory\Registry::add
         * @covers Brickoo\Library\Memory\Registry::count
         */
        public function testAddRegistrations()
        {
            $this->assertSame
            (
                $this->Registry,
                $this->Registry->add(array('name' => 'brickoo', 'town' => 'bonn'))
            );
            $this->assertEquals(2, count($this->Registry));
        }

        /**
         * Test if the registrations can be added as an array.
         * @covers Brickoo\Library\Memory\Registry::isIdentifierAvailable
         * @covers Brickoo\Library\Memory\Registry::isRegistered
         */
        public function testIsIdentifierAvailable()
        {
            $this->assertFalse($this->Registry->isIdentifierAvailable('name'));
            $this->Registry->register('name', 'john');
            $this->assertTrue($this->Registry->isIdentifierAvailable('name'));
        }

        /**
         * Test if the unvalid identifier throws an exception.
         * @covers Brickoo\Library\Memory\Registry::isIdentifierAvailable
         * @expectedException InvalidArgumentException
         */
        public function testIsIdentifierAvailableException()
        {
            $this->Registry->isIdentifierAvailable(array('wrongType'));
        }

        /**
         * Test if an key-value pair can be registered.
         * @covers Brickoo\Library\Memory\Registry::register
         */
        public function testRegister()
        {
            $this->assertSame($this->Registry, $this->Registry->register('town', 'bonn'));
        }

        /**
         * Test if using the read only the registration throws an exception.
         * @covers Brickoo\Library\Memory\Registry::register
         * @covers Brickoo\Library\Memory\Registry::setReadOnly
         * @covers Brickoo\Library\Memory\Exceptions\ReadonlyModeException
         * @expectedException Brickoo\Library\Memory\Exceptions\ReadonlyModeException
         */
        public function testRegisterReadonlyException()
        {
            $this->Registry->setReadOnly(true);
            $this->Registry->register('name', 'john');
        }

        /**
         * Test if the registration of an registered identifer throws an exception.
         * @covers Brickoo\Library\Memory\Registry::register
         * @covers Brickoo\Library\Memory\Exceptions\DuplicateRegistrationException
         * @expectedException Brickoo\Library\Memory\Exceptions\DuplicateRegistrationException
         */
        public function testRegisterRegisteredException()
        {
            $this->Registry->register('name', 'john');
            $this->Registry->register('name', 'wayne');
        }

        /**
         * Test if an registered key can be retrieved.
         * @covers Brickoo\Library\Memory\Registry::get
         */
        public function testGetRegistered()
        {
            $this->Registry->register('name' ,'john');
            $this->assertEquals('john', $this->Registry->get('name'));
        }

        /**
         * Test if using the registration of an registered identifer throws an exception.
         * @covers Brickoo\Library\Memory\Registry::get
         * @expectedException InvalidArgumentException
         */
        public function testGetRegisteredArgumentException()
        {
            $this->Registry->get(array('wrongType'));
        }

        /**
         * Test if retrieving an not registered identifer throws an exception.
         * @covers Brickoo\Library\Memory\Registry::get
         * @covers Brickoo\Library\Memory\Exceptions\IdentifierNotRegisteredException
         * @expectedException Brickoo\Library\Memory\Exceptions\IdentifierNotRegisteredException
         */
        public function testGetRegisteredException()
        {
            $this->Registry->get('name');
        }

        /**
         * Test overriding an registered key.
         * @covers Brickoo\Library\Memory\Registry::override
         */
        public function testOverride()
        {
            $this->assertSame($this->Registry, $this->Registry->override('name', 'framework'));
        }

        /**
         * Test if overriding while identifier is locked throws an exception.
         * @covers Brickoo\Library\Memory\Registry::override
         * @covers Brickoo\Library\Memory\Exceptions\IdentifierLockedException
         * @expectedException Brickoo\Library\Memory\Exceptions\IdentifierLockedException
         */
        public function testOverrideLockException()
        {
            $this->Registry->register('name', 'john');
            $this->Registry->lock('name');
            $this->Registry->override('name', 'wayne');
        }

        /**
         * Test if overriding while the Registry is in read only mode throws an exception.
         * @covers Brickoo\Library\Memory\Registry::override
         * @covers Brickoo\Library\Memory\Exceptions\ReadonlyModeException
         * @expectedException Brickoo\Library\Memory\Exceptions\ReadonlyModeException
         */
        public function testOverrideReadonlyException()
        {
            $this->Registry->register('name', 'john');
            $this->Registry->setReadOnly(true);
            $this->Registry->override('name', 'wayne');
        }

        /**
         * Test if a key can be unregistered.
         * @covers Brickoo\Library\Memory\Registry::unregister
         */
        public function testUnregister()
        {
            $this->Registry->register('name', 'john');
            $this->assertSame($this->Registry, $this->Registry->unregister('name'));
        }

        /**
         * Test if unregister of a non registred indentifier throws an exception.
         * @covers Brickoo\Library\Memory\Registry::unregister
         * @covers Brickoo\Library\Memory\Exceptions\IdentifierNotRegisteredException
         * @expectedException Brickoo\Library\Memory\Exceptions\IdentifierNotRegisteredException
         */
        public function testUnregisterException()
        {
            $this->Registry->unregister('name');
        }

        /**
         * Test if unregister while the Registry is in read only mode throws an exception.
         * @covers Brickoo\Library\Memory\Registry::unregister
         * @covers Brickoo\Library\Memory\Exceptions\ReadonlyModeException
         * @expectedException Brickoo\Library\Memory\Exceptions\ReadonlyModeException
         */
        public function testUnregisterReadonlyException()
        {
            $this->Registry->register('name', 'john');
            $this->Registry->setReadOnly(true);
            $this->Registry->unregister('name');
        }

        /**
         * Test if unregister while the Registry identifier is locked throws an exception.
         * @covers Brickoo\Library\Memory\Registry::unregister
         * @covers Brickoo\Library\Memory\Exceptions\IdentifierLockedException
         * @expectedException Brickoo\Library\Memory\Exceptions\IdentifierLockedException
         */
        public function testUnregisterLockedException()
        {
            $this->Registry->register('name', 'john');
            $this->Registry->lock('name');
            $this->Registry->unregister('name');
        }

        /**
         * Test counting registrations.
         * @covers Brickoo\Library\Memory\Registry::count
         */
        public function testCount()
        {
            $this->Registry->register('name', 'john');
            $this->assertEquals(1, count($this->Registry));
        }

        /**
         * Test counting locked registration identifiers.
         * @covers Brickoo\Library\Memory\Registry::countLocked
         */
        public function testCountLocked()
        {
            $this->Registry->register('name', 'john');
            $this->Registry->lock('name');
            $this->assertEquals(1, $this->Registry->countLocked());
        }

        /**
         * Test if a registered key can be retrieved by magic __get().
         * @covers Brickoo\Library\Memory\Registry::__get
         */
        public function testMagicFunctionGet()
        {
            $this->Registry->register('name', 'brickoo');
            $this->assertEquals('brickoo', $this->Registry->name);
        }

        /**
         * Test if a not registered key can be retrieved by magic __get() throws an exception.
         * @covers Brickoo\Library\Memory\Registry::__get
         * @covers Brickoo\Library\Memory\Exceptions\IdentifierNotRegisteredException
         * @expectedException Brickoo\Library\Memory\Exceptions\IdentifierNotRegisteredException
         */
        public function testMagicFunctionGetException()
        {
            $this->Registry->brickoo;
        }

        /**
         * Test if an unregistered key can be stored by magic __set().
         * @covers Brickoo\Library\Memory\Registry::__set
         */
        public function testMagicFunctionSet()
        {
            $this->assertEquals('brickoo', $this->Registry->name = 'brickoo');
        }

        /**
         * Test if a registered key assigned by magic __set() throws an exception.
         * @covers Brickoo\Library\Memory\Registry::__set
         * @covers Brickoo\Library\Memory\Exceptions\DuplicateRegistrationException
         * @expectedException Brickoo\Library\Memory\Exceptions\DuplicateRegistrationException
         */
        public function testMagicFunctionSetException()
        {
            $this->assertEquals('john', $this->Registry->name = 'john');
            $this->Registry->name = 'wayne';
        }

        /**
         * Test if a registerd key can be recognized by magic __isset().
         * @covers Brickoo\Library\Memory\Registry::__isset
         */
        public function testMagicFunctionIsset()
        {
            $this->assertEquals('brickoo', $this->Registry->name = 'brickoo');
            $this->assertEquals(true, isset($this->Registry->name));
        }

        /**
         * Test if a registered key can be unset by magic __unset().
         * @covers Brickoo\Library\Memory\Registry::__unset
         */
        public function testMagicFunctionUnset()
        {
            $this->assertEquals('brickoo', $this->Registry->name = 'brickoo');
            unset($this->Registry->name);
            $this->assertEquals(false, isset($this->Registry->name));
        }

        /**
         * Test if read only mode disallows write and allows read permisions.
         * @covers Brickoo\Library\Memory\Registry::isReadOnly
         */
        public function testReadonlyMode()
        {
            $this->assertFalse($this->Registry->isReadOnly());
            $this->Registry->setReadOnly(true);
            $this->assertTrue($this->Registry->isReadOnly());
        }

        /**
         * Test if a wrong type throws an exception.
         * @covers Brickoo\Library\Memory\Registry::setReadOnly
         * @expectedException InvalidArgumentException
         */
        public function testSetReadOnlyException()
        {
            $this->Registry->setReadOnly(array('wrongType'));
        }

    }