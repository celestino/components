<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Common;

use Brickoo\Component\Common\Registry;
use PHPUnit_Framework_TestCase;

/**
 * RegistryTest
 *
 * Test suite for the Registry class.
 * @see Brickoo\Component\Common\Registry
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class RegistryTest extends PHPUnit_Framework_TestCase {

    /**
     * Holds an instance of the Registry class.
     * @var \Brickoo\Component\Common\Registry
     */
    public $registry;

    /**
     * Set up the Registry object used.
     * @return void
     */
    public function setUp() {
        $this->registry = new Registry();
    }

    /**
     * @covers Brickoo\Component\Common\Registry::__construct
     * @covers Brickoo\Component\Common\Registry::getAll
     */
    public function testReadOnlyInConstructorWoksOnlyWithRegistrations() {
        $registry = new Registry([], true);
        $this->assertFalse($registry->isReadOnly());

        $registry = new Registry(["value"], true);
        $this->assertTrue($registry->isReadOnly());
    }

    /** @covers Brickoo\Component\Common\Registry::getAll */
    public function testGetRegistrations() {
        $this->assertInternalType("array", $this->registry->getAll());
        $this->registry->register("name", "john");
        $this->assertArrayHasKey("name", $this->registry->getAll());
    }

     /** @covers Brickoo\Component\Common\Registry::add */
    public function testAddRegistrations() {
        $expectedRegistrations = array("name" => "brickoo", "town" => "bonn");
        $this->assertSame(
            $this->registry,
            $this->registry->add($expectedRegistrations)
        );
        $this->assertAttributeEquals($expectedRegistrations, "registrations", $this->registry);
    }

    /**
     * @covers Brickoo\Component\Common\Registry::isIdentifierAvailable
     * @covers Brickoo\Component\Common\Registry::isRegistered
     */
    public function testIsIdentifierAvailable() {
        $this->assertFalse($this->registry->isIdentifierAvailable("name"));
        $this->registry->register("name", "john");
        $this->assertTrue($this->registry->isIdentifierAvailable("name"));
    }

    /**
     * @covers Brickoo\Component\Common\Registry::isIdentifierAvailable
     * @expectedException \InvalidArgumentException
     */
    public function testIsIdentifierAvailableException() {
        $this->registry->isIdentifierAvailable(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Common\Registry::register
     * @covers Brickoo\Component\Common\Registry::set
     */
    public function testRegister() {
        $this->assertSame($this->registry, $this->registry->register("town", "bonn"));
    }

    /**
     * @covers Brickoo\Component\Common\Registry::register
     * @covers Brickoo\Component\Common\Registry::set
     * @covers Brickoo\Component\Common\Registry::setReadOnly
     * @covers Brickoo\Component\Common\Exception\ReadonlyModeException
     * @expectedException \Brickoo\Component\Common\Exception\ReadonlyModeException
     */
    public function testRegisterReadonlyException() {
        $this->registry->setReadOnly(true);
        $this->registry->register("name", "john");
    }

    /**
     * @covers Brickoo\Component\Common\Registry::register
     * @covers Brickoo\Component\Common\Exception\DuplicateRegistrationException
     * @expectedException \Brickoo\Component\Common\Exception\DuplicateRegistrationException
     */
    public function testRegisterRegisteredException() {
        $this->registry->register("name", "john");
        $this->registry->register("name", "wayne");
    }

    /** @covers Brickoo\Component\Common\Registry::get */
    public function testGetRegistered() {
        $this->registry->register("name" ,"john");
        $this->assertEquals("john", $this->registry->get("name"));
    }

    /**
     * @covers Brickoo\Component\Common\Registry::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetRegisteredArgumentException() {
        $this->registry->get(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Common\Registry::get
     * @covers Brickoo\Component\Common\Exception\IdentifierNotRegisteredException
     * @expectedException \Brickoo\Component\Common\Exception\IdentifierNotRegisteredException
     */
    public function testGetRegisteredException() {
        $this->registry->get("name");
    }

    /**
     * @covers Brickoo\Component\Common\Registry::override
     * @covers Brickoo\Component\Common\Registry::register
     */
    public function testOverride() {
        $this->assertSame($this->registry, $this->registry->override("name", "framework"));
    }

    /**
     * @covers Brickoo\Component\Common\Registry::override
     * @covers Brickoo\Component\Common\Exception\IdentifierLockedException
     * @expectedException \Brickoo\Component\Common\Exception\IdentifierLockedException
     */
    public function testOverrideLockException() {
        $this->registry->register("name", "john");
        $this->registry->lock("name");
        $this->registry->override("name", "wayne");
    }

    /**
     * @covers Brickoo\Component\Common\Registry::override
     * @covers Brickoo\Component\Common\Registry::set
     * @covers Brickoo\Component\Common\Exception\ReadonlyModeException
     * @expectedException \Brickoo\Component\Common\Exception\ReadonlyModeException
     */
    public function testOverrideReadonlyException() {
        $this->registry->register("name", "john");
        $this->registry->setReadOnly(true);
        $this->registry->override("name", "wayne");
    }

    /** @covers Brickoo\Component\Common\Registry::unregister */
    public function testUnregister() {
        $this->registry->register("name", "john");
        $this->assertSame($this->registry, $this->registry->unregister("name"));
    }

    /**
     * @covers Brickoo\Component\Common\Registry::unregister
     * @covers Brickoo\Component\Common\Exception\IdentifierNotRegisteredException
     * @expectedException \Brickoo\Component\Common\Exception\IdentifierNotRegisteredException
     */
    public function testUnregisterException() {
        $this->registry->unregister("name");
    }

    /**
     * @covers Brickoo\Component\Common\Registry::unregister
     * @covers Brickoo\Component\Common\Exception\ReadonlyModeException
     * @expectedException \Brickoo\Component\Common\Exception\ReadonlyModeException
     */
    public function testUnregisterReadonlyException() {
        $this->registry->register("name", "john");
        $this->registry->setReadOnly(true);
        $this->registry->unregister("name");
    }

    /**
     * @covers Brickoo\Component\Common\Registry::unregister
     * @covers Brickoo\Component\Common\Exception\IdentifierLockedException
     * @expectedException \Brickoo\Component\Common\Exception\IdentifierLockedException
     */
    public function testUnregisterLockedException() {
        $this->registry->register("name", "john");
        $this->registry->lock("name");
        $this->registry->unregister("name");
    }

    /** @covers Brickoo\Component\Common\Registry::count */
    public function testCount() {
        $this->registry->register("name", "john");
        $this->assertEquals(1, count($this->registry));
    }

    /** @covers Brickoo\Component\Common\Registry::countLocked */
    public function testCountLocked() {
        $this->registry->register("name", "john");
        $this->registry->lock("name");
        $this->assertEquals(1, $this->registry->countLocked());
    }

    /** @covers Brickoo\Component\Common\Registry::isReadOnly */
    public function testReadonlyMode() {
        $this->assertFalse($this->registry->isReadOnly());
        $this->registry->setReadOnly(true);
        $this->assertTrue($this->registry->isReadOnly());
    }

    /**
     * @covers Brickoo\Component\Common\Registry::setReadOnly
     * @expectedException \InvalidArgumentException
     */
    public function testSetReadOnlyException() {
        $this->registry->setReadOnly(array("wrongType"));
    }

}
