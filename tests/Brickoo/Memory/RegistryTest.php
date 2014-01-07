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

    namespace Tests\Brickoo\Memory;

    use Brickoo\Memory\Registry;

    /**
     * RegistryTest
     *
     * Test suite for the Registry class.
     * @see Brickoo\Memory\Registry
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RegistryTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Registry class.
         * @var object Registry
         */
        public $Registry;

        /**
         * Set up the Registry object used.
         * @return void
         */
        public function setUp() {
            $this->Registry = new Registry();
        }

        /**
         * @covers Brickoo\Memory\Registry::__construct
         */
        public function testRegistryConstructor() {
            $expectedRegistrations = array("name" => "john");
            $expectedMode = true;
            $Registry = new Registry($expectedRegistrations, $expectedMode);
            $this->assertInstanceOf('\Brickoo\Memory\Registry', $Registry);
            $this->assertAttributeEquals($expectedRegistrations, "registrations", $Registry);
            $this->assertAttributeEquals($expectedMode, "readOnly", $Registry);
        }

        /**
         * @covers Brickoo\Memory\Registry::getAll
         */
        public function testGetRegistrations() {
            $this->assertInternalType('array', $this->Registry->getAll());
            $this->Registry->register('name', 'john');
            $this->assertArrayHasKey('name', $this->Registry->getAll());
        }

         /**
          * @covers Brickoo\Memory\Registry::add
          */
        public function testAddRegistrations() {
            $expectedRegistrations = array('name' => 'brickoo', 'town' => 'bonn');
            $this->assertSame(
                $this->Registry,
                $this->Registry->add($expectedRegistrations)
            );
            $this->assertAttributeEquals($expectedRegistrations, "registrations", $this->Registry);
        }

        /**
         * @covers Brickoo\Memory\Registry::isIdentifierAvailable
         * @covers Brickoo\Memory\Registry::isRegistered
         */
        public function testIsIdentifierAvailable() {
            $this->assertFalse($this->Registry->isIdentifierAvailable('name'));
            $this->Registry->register('name', 'john');
            $this->assertTrue($this->Registry->isIdentifierAvailable('name'));
        }

        /**
         * @covers Brickoo\Memory\Registry::isIdentifierAvailable
         * @expectedException InvalidArgumentException
         */
        public function testIsIdentifierAvailableException() {
            $this->Registry->isIdentifierAvailable(array('wrongType'));
        }

        /**
         * @covers Brickoo\Memory\Registry::register
         */
        public function testRegister() {
            $this->assertSame($this->Registry, $this->Registry->register('town', 'bonn'));
        }

        /**
         * @covers Brickoo\Memory\Registry::register
         * @covers Brickoo\Memory\Registry::setReadOnly
         * @covers Brickoo\Memory\Exceptions\ReadonlyModeException
         * @expectedException Brickoo\Memory\Exceptions\ReadonlyModeException
         */
        public function testRegisterReadonlyException() {
            $this->Registry->setReadOnly(true);
            $this->Registry->register('name', 'john');
        }

        /**
         * @covers Brickoo\Memory\Registry::register
         * @covers Brickoo\Memory\Exceptions\DuplicateRegistrationException
         * @expectedException Brickoo\Memory\Exceptions\DuplicateRegistrationException
         */
        public function testRegisterRegisteredException() {
            $this->Registry->register('name', 'john');
            $this->Registry->register('name', 'wayne');
        }

        /**
         * @covers Brickoo\Memory\Registry::get
         */
        public function testGetRegistered() {
            $this->Registry->register('name' ,'john');
            $this->assertEquals('john', $this->Registry->get('name'));
        }

        /**
         * @covers Brickoo\Memory\Registry::get
         * @expectedException InvalidArgumentException
         */
        public function testGetRegisteredArgumentException() {
            $this->Registry->get(array('wrongType'));
        }

        /**
         * @covers Brickoo\Memory\Registry::get
         * @covers Brickoo\Memory\Exceptions\IdentifierNotRegisteredException
         * @expectedException Brickoo\Memory\Exceptions\IdentifierNotRegisteredException
         */
        public function testGetRegisteredException() {
            $this->Registry->get('name');
        }

        /**
         * @covers Brickoo\Memory\Registry::override
         */
        public function testOverride() {
            $this->assertSame($this->Registry, $this->Registry->override('name', 'framework'));
        }

        /**
         * @covers Brickoo\Memory\Registry::override
         * @covers Brickoo\Memory\Exceptions\IdentifierLockedException
         * @expectedException Brickoo\Memory\Exceptions\IdentifierLockedException
         */
        public function testOverrideLockException() {
            $this->Registry->register('name', 'john');
            $this->Registry->lock('name');
            $this->Registry->override('name', 'wayne');
        }

        /**
         * @covers Brickoo\Memory\Registry::override
         * @covers Brickoo\Memory\Exceptions\ReadonlyModeException
         * @expectedException Brickoo\Memory\Exceptions\ReadonlyModeException
         */
        public function testOverrideReadonlyException() {
            $this->Registry->register('name', 'john');
            $this->Registry->setReadOnly(true);
            $this->Registry->override('name', 'wayne');
        }

        /**
         * @covers Brickoo\Memory\Registry::unregister
         */
        public function testUnregister() {
            $this->Registry->register('name', 'john');
            $this->assertSame($this->Registry, $this->Registry->unregister('name'));
        }

        /**
         * @covers Brickoo\Memory\Registry::unregister
         * @covers Brickoo\Memory\Exceptions\IdentifierNotRegisteredException
         * @expectedException Brickoo\Memory\Exceptions\IdentifierNotRegisteredException
         */
        public function testUnregisterException() {
            $this->Registry->unregister('name');
        }

        /**
         * @covers Brickoo\Memory\Registry::unregister
         * @covers Brickoo\Memory\Exceptions\ReadonlyModeException
         * @expectedException Brickoo\Memory\Exceptions\ReadonlyModeException
         */
        public function testUnregisterReadonlyException() {
            $this->Registry->register('name', 'john');
            $this->Registry->setReadOnly(true);
            $this->Registry->unregister('name');
        }

        /**
         * @covers Brickoo\Memory\Registry::unregister
         * @covers Brickoo\Memory\Exceptions\IdentifierLockedException
         * @expectedException Brickoo\Memory\Exceptions\IdentifierLockedException
         */
        public function testUnregisterLockedException() {
            $this->Registry->register('name', 'john');
            $this->Registry->lock('name');
            $this->Registry->unregister('name');
        }

        /**
         * @covers Brickoo\Memory\Registry::count
         */
        public function testCount() {
            $this->Registry->register('name', 'john');
            $this->assertEquals(1, count($this->Registry));
        }

        /**
         * @covers Brickoo\Memory\Registry::countLocked
         */
        public function testCountLocked() {
            $this->Registry->register('name', 'john');
            $this->Registry->lock('name');
            $this->assertEquals(1, $this->Registry->countLocked());
        }

        /**
         * @covers Brickoo\Memory\Registry::__get
         */
        public function testMagicFunctionGet() {
            $this->Registry->register('name', 'brickoo');
            $this->assertEquals('brickoo', $this->Registry->name);
        }

        /**
         * @covers Brickoo\Memory\Registry::__get
         * @covers Brickoo\Memory\Exceptions\IdentifierNotRegisteredException
         * @expectedException Brickoo\Memory\Exceptions\IdentifierNotRegisteredException
         */
        public function testMagicFunctionGetException() {
            $this->Registry->brickoo;
        }

        /**
         * @covers Brickoo\Memory\Registry::__set
         */
        public function testMagicFunctionSet() {
            $this->assertEquals('brickoo', $this->Registry->name = 'brickoo');
        }

        /**
         * @covers Brickoo\Memory\Registry::__set
         * @covers Brickoo\Memory\Exceptions\DuplicateRegistrationException
         * @expectedException Brickoo\Memory\Exceptions\DuplicateRegistrationException
         */
        public function testMagicFunctionSetException() {
            $this->assertEquals('john', $this->Registry->name = 'john');
            $this->Registry->name = 'wayne';
        }

        /**
         * @covers Brickoo\Memory\Registry::__isset
         */
        public function testMagicFunctionIsset() {
            $this->assertEquals('brickoo', $this->Registry->name = 'brickoo');
            $this->assertEquals(true, isset($this->Registry->name));
        }

        /**
         * @covers Brickoo\Memory\Registry::__unset
         */
        public function testMagicFunctionUnset() {
            $this->assertEquals('brickoo', $this->Registry->name = 'brickoo');
            unset($this->Registry->name);
            $this->assertEquals(false, isset($this->Registry->name));
        }

        /**
         * @covers Brickoo\Memory\Registry::isReadOnly
         */
        public function testReadonlyMode() {
            $this->assertFalse($this->Registry->isReadOnly());
            $this->Registry->setReadOnly(true);
            $this->assertTrue($this->Registry->isReadOnly());
        }

        /**
         * @covers Brickoo\Memory\Registry::setReadOnly
         * @expectedException InvalidArgumentException
         */
        public function testSetReadOnlyException() {
            $this->Registry->setReadOnly(array('wrongType'));
        }

    }