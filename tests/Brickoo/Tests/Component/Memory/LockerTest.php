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

namespace Brickoo\Tests\Component\Memory;

use Brickoo\Component\Memory\Locker,
    PHPUnit_Framework_TestCase;

/**
 * LockerTest
 *
 * Test suite for the Locker class.
 * @see Brickoo\Component\Memory\Locker
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LockerTest extends PHPUnit_Framework_TestCase {

    /**
     * Holds an instance of the lockerTestable class.
     * @var \Brickoo\Component\Memory\Locker
     */
    public $lockerTestable;

    /**
     * Set up the lockerTestable object used.
     * @return void
     */
    public function setUp() {
        $this->lockerTestable = new LockerTestable();
    }

    /**
     * @covers Brickoo\Component\Memory\Locker::__construct
     * @covers Brickoo\Component\Memory\Locker::lock
     * @covers Brickoo\Component\Memory\Locker::unlock
     * @covers Brickoo\Component\Memory\Locker::isLocked
     */
    public function testLockAndUnlockRoutine() {
        $this->assertInternalType("string", ($unlockKey = $this->lockerTestable->lock("name")));
        $this->assertTrue($this->lockerTestable->isLocked("name"));
        $this->assertSame($this->lockerTestable, $this->lockerTestable->unlock("name", $unlockKey));
        $this->assertFalse($this->lockerTestable->isLocked("name"));
    }

    /**
     * @covers Brickoo\Component\Memory\Locker::lock
     * @expectedException InvalidArgumentException
     */
    public function testLockArgumentException() {
        $this->lockerTestable->lock(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Memory\Locker::lock
     * @covers Brickoo\Component\Memory\Exception\LockFailedException::__construct
     * @expectedException  Brickoo\Component\Memory\Exception\LockFailedException
     */
    public function testLockFailedException() {
        $this->lockerTestable->lock("name");
        $this->lockerTestable->lock("name");
    }

    /**
     * @covers Brickoo\Component\Memory\Locker::unlock
     * @covers Brickoo\Component\Memory\Exception\UnlockFailedException::__construct
     * @expectedException Brickoo\Component\Memory\Exception\UnlockFailedException
     */
    public function testUnLockWrongUnlockKeyException() {
        $this->lockerTestable->lock("name");
        $this->lockerTestable->unlock("name", "invalidKey");
    }

    /**
     * @covers Brickoo\Component\Memory\Locker::unlock
     * @expectedException  InvalidArgumentException
     */
    public function testUnlockInvalidArgumentException() {
        $this->lockerTestable->unlock(array("wrongType"), "invalidKey");
    }

    /**
     * @covers Brickoo\Component\Memory\Locker::unlock
     * @covers Brickoo\Component\Memory\Exception\UnlockFailedException
     * @expectedException Brickoo\Component\Memory\Exception\UnlockFailedException
     */
    public function testUnlockFailedException() {
        $this->lockerTestable->unlock("notLocked", "invalidKey");
    }

    /**
     * @covers Brickoo\Component\Memory\Locker::isLocked
     * @expectedException InvalidArgumentException
     */
    public function testIsLockedArgumentException() {
        $this->lockerTestable->isLocked(array("wrongType"));
    }

    /** @covers Brickoo\Component\Memory\Locker::count */
    public function testCount() {
        $this->lockerTestable->lock("name");
        $this->lockerTestable->lock("town");
        $this->lockerTestable->lock("country");
        $this->assertEquals(3, count($this->lockerTestable));
    }

}

class LockerTestable extends Locker {

    /**
     * Abstract method to check if the main class has the identifier.
     * @param string|integer $identifier the identifier to check
     * @see Brickoo\Component\Memory\Locker::isIdentifierAvailable()
     * @return boolean true
     */
    public function isIdentifierAvailable($identifier) {
        return true;
    }

}